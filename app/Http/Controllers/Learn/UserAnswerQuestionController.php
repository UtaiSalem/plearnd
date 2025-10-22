<?php

namespace App\Http\Controllers\Learn;
use App\Http\Controllers\Controller;
use DB;
use App\Models\Question;
use App\Models\CourseQuiz;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Models\CourseQuizResult;
use App\Models\UserAnswerQuestion;
use App\Services\QuizEfficiencyService;
use App\Constants\QuizConstants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class UserAnswerQuestionController extends Controller
{
    protected $auth;
    protected $quizEfficiencyService;

    public function __construct(Auth $auth, QuizEfficiencyService $quizEfficiencyService)
    {
        $this->auth = $auth;
        $this->quizEfficiencyService = $quizEfficiencyService;
    }

    /**
     * Store a new user answer for a question.
     */
    public function store(CourseQuiz $quiz, Question $question, Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'answer_id' => 'required|exists:question_options,id', // Assuming options table stores possible answers
            'started_at' => 'nullable|date',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($quiz, $question, $request) {
            $isCorrectAnswer = $request->answer_id === $question->correct_option_id;
            $points = $isCorrectAnswer ? $question->points : 0;
            $started_at = $request->started_at;

            // Create user answer record
            $userAnswerQuestion = $this->createUserAnswerQuestion($request, $quiz, $question, $points);

            // Update course quiz result and metrics
            $this->updateCourseQuizResult($quiz, $started_at);

            // Update course member score if the answer is correct
            if ($isCorrectAnswer) {
                $this->updateCourseMemberScore($quiz->course_id, $points, 0);
            }

            return $this->generateResponse($userAnswerQuestion, $points);
        });
    }

    /**
     * Update an existing user answer for a question.
     */
    public function update(CourseQuiz $quiz, Question $question, UserAnswerQuestion $answer, Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'answer_id' => 'required|exists:question_options,id', // Assuming options table stores possible answers
            'started_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        return DB::transaction(function () use ($quiz, $question, $answer, $request) {
            $oldAnswer = $answer->answer_id;
            $newAnswer = $request->answer_id;
            $correctAnswer = $question->correct_option_id;
            $isOldAnswerCorrect = $oldAnswer === $correctAnswer;
            $isNewAnswerCorrect = $newAnswer === $correctAnswer;

            // Update the user's answer record
            $answer->update([
                'answer_id' => $newAnswer,
                'correct_option_id' => $correctAnswer,
                'points' => $isNewAnswerCorrect ? $question->points : 0,
                'edit_count' => $answer->edit_count + 1,
            ]);

            // Update quiz result and metrics
            $this->updateQuizResultAndMemberScore($quiz, $answer, $isOldAnswerCorrect, $isNewAnswerCorrect, $question->points, $request->started_at);
            // $this->updateCourseQuizResult($quiz, $request->started_at);

            return response()->json([
                'success' => true,
                'message' => 'แก้ไข',
                'authAnswerQuestion' => $answer->id,
                'points' => $answer->points,
            ], 200);
        });
    }

    /**
     * Update or create a course quiz result record.
     */
    private function updateCourseQuizResult($quiz, $started_at = null)
    {
        $courseQuizResult = CourseQuizResult::firstOrCreate(
            [
                'course_id' => $quiz->course_id,
                'user_id' => auth()->id(),
                'quiz_id' => $quiz->id,
            ],
            [
                'started_at' => $started_at ?? now(),
            ]
        );

        // Update quiz metrics (e.g., score, percentage, status)
        $this->updateQuizMetrics($courseQuizResult, $quiz, $started_at);

        return $courseQuizResult;
    }

    /**
     * Update quiz result metrics and course member score after editing an answer.
     */
    private function updateQuizResultAndMemberScore($quiz, $answer, $isOldAnswerCorrect, $isNewAnswerCorrect, $points, $started_at)
    {
        $courseQuizResult = CourseQuizResult::where([
            'course_id' => $answer->course_id,
            'user_id' => $answer->user_id,
            'quiz_id' => $answer->quiz_id,
        ])->first();

        if (!$courseQuizResult) {
            Log::error('Course quiz result not found for answer ID: ' . $answer->id);
            return response()->json(['success' => false, 'message' => 'Course quiz result not found'], 404);
        }

        $oldQuizResultScore = $courseQuizResult->score;

        // Adjust score based on old and new answers
        if ($isOldAnswerCorrect && !$isNewAnswerCorrect) {
            $courseQuizResult->score -= $points; // Deduct points for incorrect new answer
        } elseif (!$isOldAnswerCorrect && $isNewAnswerCorrect) {
            $courseQuizResult->score += $points; // Add points for correct new answer
        }

        $courseQuizResult->save();

        // Update quiz metrics (e.g., score, percentage, status)
        $this->updateQuizMetrics($courseQuizResult, $quiz, $started_at);

        // Update course member score
        $this->updateCourseMemberScore($quiz->course_id, $courseQuizResult->score, $oldQuizResultScore);

        return $courseQuizResult;
    }

    /**
     * Update course member's achieved score.
     */
    private function updateCourseMemberScore($courseId, $points, $oldQuizResultScore = 0)
    {
        $courseMember = CourseMember::where('course_id', $courseId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$courseMember) {
            Log::error('Course member not found for course ID: ' . $courseId);
            return response()->json(['success' => false, 'message' => 'Course member not found'], 404);
        }

        $courseMember->decrement('achieved_score', $oldQuizResultScore);
        $courseMember->increment('achieved_score', $points);
        return $courseMember;
    }

    /**
     * Update quiz metrics (e.g., attempted questions, correct answers, percentage, status).
     */
    private function updateQuizMetrics($courseQuizResult, $quiz, $started_at = null)
    {

        $quiz_user_answers = UserAnswerQuestion::where('user_id', auth()->id())
            ->where('quiz_id', $quiz->id)
            ->get();

        $courseQuizResult->attempted_questions = $quiz_user_answers->count();
        //$courseQuizResult->correct_answers = $quiz_user_answers->where('points', '>', 0)->count();
        //$courseQuizResult->incorrect_answers = $quiz_user_answers->where('points', '=', 0)->count();
		$courseQuizResult->correct_answers = $quiz_user_answers->filter(function ($answer) {
            return $answer->correct_option_id === $answer->answer_id;
        })->count();
        
        $courseQuizResult->incorrect_answers = $quiz_user_answers->filter(function ($answer) {
            return $answer->correct_option_id !== $answer->answer_id;
        })->count();
		
        $courseQuizResult->score = $quiz_user_answers->sum('points');
        $courseQuizResult->percentage = $this->calculatePercentage($quiz->total_score, $courseQuizResult->score);
        $courseQuizResult->status = $courseQuizResult->percentage >= $quiz->passing_score
            ? QuizConstants::STATUS_PASSED
            : QuizConstants::STATUS_FAILED;

        if ($started_at) {
            $courseQuizResult->duration += now()->diffInSeconds($started_at);
        }

        $courseQuizResult->efficiency = $this->quizEfficiencyService->calculateEfficiency($courseQuizResult, $quiz);
        $courseQuizResult->save();
    }

    /**
     * Calculate percentage score.
     */
    private function calculatePercentage($totalScore, $score)
    {
        return $totalScore > 0 || $score > 0 ? ($score / $totalScore) * 100 : 0;
    }

    /**
     * Create a new user answer record.
     */
    private function createUserAnswerQuestion($request, $quiz, $question, $points)
    {
        try {
            return auth()->user()->answerQuestions()->create([
                'course_id' => $request->course_id,
                'quiz_id' => $quiz->id,
                'question_id' => $question->id,
                'correct_option_id' => $question->correct_option_id,
                'answer_id' => $request->answer_id,
                'points' => $points,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create user answer question: ' . $e->getMessage());
            throw $e; // Re-throw the exception for transaction rollback
        }
    }

    /**
     * Generate a JSON response.
     */
    private function generateResponse($userAnswerQuestion, $points)
    {
        if ($userAnswerQuestion) {
            return response()->json([
                'success' => true,
                'message' => 'บันทึก',
                'authAnswerQuestion' => $userAnswerQuestion->id,
                'points' => $points,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'points' => 0
        ], 201);
    }
}