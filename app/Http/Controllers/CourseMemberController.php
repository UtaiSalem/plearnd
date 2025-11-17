<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Course;
use App\Models\CourseGroup;
use App\Models\CourseMember;
use Illuminate\Http\Request;
use App\Models\CourseQuizResult;
use App\Models\CourseGroupMember;
use App\Models\UserAnswerQuestion;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\CourseQuizResource;
use App\Http\Resources\CourseGroupResource;
use App\Http\Resources\CourseMemberResource;
use App\Http\Resources\AssignmentAnswerResource;

class CourseMemberController extends Controller
{
    public function index(Course $course)
    {

        return Inertia::render('Learn/Course/Member/Members', [
            'course'                => new CourseResource($course),
            'members'               => CourseMemberResource::collection($course->courseMembers()->orderBy('order_number')->paginate()),
            'groups'                => CourseGroupResource::collection($course->courseGroups),
            'isCourseAdmin'         => $course->user_id === auth()->id(),
            'courseMemberOfAuth'    => $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);
    }

    public function show(Course $course, CourseGroup $group, CourseMember $member)
    {
        return Inertia::render('Learn/Course/Member/Member', [
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->courseLessons),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'member'        => new CourseMemberResource($member)
        ]);
    }

    public function storemember(Course $course, Request $request)
    {
        if (auth()->user()->pp < $course->tuition_fees) {
            return response()->json([
                'success' => false,
                'msg'     => 'แต้มสะสมไม่เพียงพอ กรุณาเติมแต้มสะสมก่อนสมัครสมาชิก'
            ], 201);
        }

        $course_member = CourseMember::where('course_id', $course->id)->where('user_id', auth()->id())->first();

        if (!$course_member) {

            $courseAutoAcceptMembers = $course->courseSettings->auto_accept_members === 0 ? 0 : 1;

            $new_course_member = new CourseMember();
            $new_course_member->user_id     = auth()->id();
            $new_course_member->course_id   = $course->id;
            $new_course_member->group_id    = $request->group_id ?? null;
            $new_course_member->status      = $courseAutoAcceptMembers;
            $new_course_member->course_member_status      = $courseAutoAcceptMembers;
            $new_course_member->save();

            $newCourseGroupMember = new CourseGroupMember();
            $newCourseGroupMember->user_id      = auth()->id();
            $newCourseGroupMember->course_id    = $new_course_member->course_id;
            $newCourseGroupMember->group_id     = $new_course_member->group_id;
            $newCourseGroupMember->status       = $courseAutoAcceptMembers;
            $newCourseGroupMember->save();
            
        }else{

            $course_member->group_id = $request->group_id;
            $course_member->save();
            $new_course_member = $course_member;

            $courseGroupMember = CourseGroupMember::where('course_id', $course->id)->where('user_id', auth()->id())->first();
            $courseGroupMember->group_id = $request->group_id;
            $courseGroupMember->save();
        }

        return response()->json([
            'success' => true,
            'newCourseMember' => CourseMember::find($new_course_member->id),
            // 'newCourseMember' => new CourseMemberResource(CourseMember::find($new_course_member->id)),
            // 'setting' =>  $curent_member_status,
        ], 200);
    }

    public function destroy(Course $course, CourseMember $member)
    {     
        try {

            $member_group = CourseGroupMember::where('group_id', $member->group_id)->where('user_id', $member->user_id)->first();
            if ($member_group) {
                $member_group->delete();
            }

            $member->delete();
            
            return response()->json([
                'success' => true,
            ], 200);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function setActiveTab(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'last_accessed_tab' => $request->tab
        ]);
    }
    public function setActiveGroupTab(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'last_accessed_group_tab' => $request->group_tab
        ]);

        return response()->json([
            'success' => true,
        ], 200);
    }

    //function update
    public function update(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'member_name'   => $request->member_name,
            'order_number'  => $request->order_number,
            'member_code'   => $request->member_code,
        ]);

        if($member->user_id === auth()->id()){
            $member->update([
                'notes_comments' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'notes_comments' => $member->notes_comments
        ], 200);
    }

    // function delete course's member
    public function deleteCourseMember(Course $course, CourseMember $member)
    {
        
        try {
            $member_group = CourseGroupMember::where('course_id', $course->id)
                                                ->where('group_id', $member->group_id ?? null)
                                                ->where('user_id', $member->user_id)
                                                ->first();
            
            $assignments = $course->assignments()->get();
            if ($assignments->count() > 0) {
                foreach ($assignments as $assignment) {
                    $user_assignment_answers =  AssignmentAnswerResource::collection($assignment->answers()->where('user_id', $member->user_id)->get());
                    foreach ($user_assignment_answers as $user_assignment_answer) {
                        if($user_assignment_answer->images->count() > 0) {
                            foreach ($user_assignment_answer->images as $image) {
                                Storage::disk('public')->delete('images/courses/assignments/answers/'. $image->filename);
                            }
                            $user_assignment_answer->images()->delete();
                        }
                        $user_assignment_answer->delete();
                    }            
                }
            }

            $user_answer_questions = UserAnswerQuestion::where('course_id', $course->id)->where('user_id', $member->user_id)->delete();
            $course_quiz_results = CourseQuizResult::where('course_id', $course->id)->where('user_id', $member->user_id)->delete();

            if ($member_group) $member_group->delete();

            $member->delete();
            
            return response()->json([
                'success' => true,
            ], 200);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    //function update member bonus points
    public function updateBonusPoints(Course $course, CourseMember $member, Request $request)
    {
        $member->increment('bonus_points', $request->bonus_points);

        $member->update([
            'grade_progress' => $this->calculateGrade((($member->achieved_score+$request->bonus_points) / $course->total_score) * 100),
        ]);

        $member->refresh();
        
        return response()->json([
            'success' => true,          
        ], 200);
    }

    public function calculateGrade($score) {
        if ($score >= 80) {
            return '4';
        } else if ($score >= 75) {
            return '3.5';
        } else if ($score >= 70) {
            return '3';
        } else if ($score >= 65) {
            return '2.5';
        } else if ($score >= 60) {
            return '2';
        } else if ($score >= 55) {
            return '1.5';
        } else if ($score >= 50) {
            return '1';
        } else {
            return '0';
        }
    }

    public function updateGradeProgress(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'grade_progress' => $request->grade_progress,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function updateNotesComments(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'notes_comments' => $request->notes_comments,
        ]);

        return response()->json([
            'success' => true,
            'notes_comments' => $member->notes_comments
        ]);
    }

    public function getMembersRequesters(Course $course)
    {
        $members = $course->courseMembers()->where('course_member_status', 0)->latest()->paginate();

        return Inertia::render('Learn/Course/CourseMemberRequesters', [
            'course' => new CourseResource($course),
            'groups' => CourseGroupResource::collection($course->courseGroups),
            'members' => CourseMemberResource::collection($members),
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'courseMemberOfAuth' => $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);
    }

    public function memberSettings(Course $course, CourseMember $course_member)
    {
        // โหลด assignments พร้อมกับ answers ของ member คนนั้นๆ พร้อม relationships ที่จำเป็น
        $assignments = $course->courseAssignments()
            ->with([
                'answers' => function($query) use ($course_member) {
                    $query->where('user_id', $course_member->user_id);
                },
                'answers.user',
                'answers.assignment',
                'images'
            ])
            ->get();

        // สร้าง collection ของ AssignmentResource พร้อม set targetUserId
        $assignmentResources = $assignments->map(function($assignment) use ($course_member) {
            $resource = new AssignmentResource($assignment);
            $resource->setTargetUserId($course_member->user_id);
            return $resource;
        });

        return Inertia::render('Learn/Course/Member/MemberSettings', [
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->courseLessons),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'member'        => new CourseMemberResource($course_member),
            'assignments'   => $assignmentResources, 
            'quizzes'       => CourseQuizResource::collection($course->courseQuizzes),
            'courseMemberOfAuth' => $course->courseMembers()->where('user_id', auth()->id())->first(),
        ]);
    }

    public function memberProgress(Course $course, CourseMember $course_member)
    {
        // โหลด assignments พร้อมกับ answers ของ member คนนั้นๆ พร้อม relationships ที่จำเป็น
        $assignments = $course->courseAssignments()
            ->with([
                'answers' => function($query) use ($course_member) {
                    $query->where('user_id', $course_member->user_id);
                },
                'answers.user',
                'answers.assignment',
                'images'
            ])
            ->get();

        // สร้าง collection ของ AssignmentResource พร้อม set targetUserId
        $assignmentResources = $assignments->map(function($assignment) use ($course_member) {
            $resource = new AssignmentResource($assignment);
            $resource->setTargetUserId($course_member->user_id);
            return $resource;
        });

        return Inertia::render('Learn/Course/Member/MemberSettings', [
            'isCourseAdmin' => $course->user_id === auth()->id(),
            'course'        => new CourseResource($course),
            'lessons'       => LessonResource::collection($course->courseLessons),
            'groups'        => CourseGroupResource::collection($course->courseGroups),
            'member'        => new CourseMemberResource($course_member),
            'assignments'   => $assignmentResources, 
            'quizzes'       => CourseQuizResource::collection($course->courseQuizzes),
            'courseMemberOfAuth' => $course_member,
        ]);
    }

    public function updateOrderNumber(Course $course, CourseMember $member, Request $request)
    {
        $member->update([
            'order_number' => $request->order_number,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Process and validate member scores for assignments and quizzes
     * This method handles duplicate quiz answers and calculates correct scores
     */
    public function processMemberScores(Course $course)
    {
        // Get all course members
        $courseMembers = $course->courseMembers()->with('user')->get();
        
        foreach ($courseMembers as $member) {
            // Process quiz scores - handle duplicate answers
            $this->processQuizScores($course, $member);
            
            // Calculate total score from assignments and quizzes
            $this->calculateTotalScore($course, $member);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Member scores processed successfully'
        ]);
    }
    
    /**
     * Process quiz scores and handle duplicate answers
     */
    private function processQuizScores(Course $course, CourseMember $member)
    {
        $quizzes = $course->courseQuizzes()->with('questions')->get();
        
        foreach ($quizzes as $quiz) {
            // Get all answers for this member and quiz
            $userAnswers = UserAnswerQuestion::where('user_id', $member->user_id)
                ->where('quiz_id', $quiz->id)
                ->with('question')
                ->get();
            
            // Group answers by question_id to find duplicates
            $answersByQuestion = $userAnswers->groupBy('question_id');
            
            foreach ($answersByQuestion as $questionId => $answers) {
                if ($answers->count() > 1) {
                    // Found duplicate answers, process them
                    $this->handleDuplicateAnswers($answers, $member, $quiz);
                }
            }
            
            // Calculate quiz score after processing duplicates
            $this->calculateQuizScore($course, $member, $quiz);
        }
    }
    
    /**
     * Handle duplicate answers for a question
     * Keep only the latest correct answer, or the latest answer if none are correct
     */
    private function handleDuplicateAnswers($answers, CourseMember $member, $quiz)
    {
        $correctAnswers = $answers->filter(function ($answer) {
            return $answer->is_correct;
        });
        
        if ($correctAnswers->count() > 0) {
            // Keep only the latest correct answer
            $latestCorrectAnswer = $correctAnswers->sortByDesc('created_at')->first();
            $answersToKeep = $latestCorrectAnswer;
        } else {
            // No correct answers, keep the latest one
            $latestAnswer = $answers->sortByDesc('created_at')->first();
            $answersToKeep = $latestAnswer;
        }
        
        // Delete all answers except the one to keep
        foreach ($answers as $answer) {
            if ($answer->id !== $answersToKeep->id) {
                $answer->delete();
            }
        }
    }
    
    /**
     * Calculate and update quiz score for a member
     */
    private function calculateQuizScore(Course $course, CourseMember $member, $quiz)
    {
        // Get all correct answers for this member and quiz
        $correctAnswersCount = UserAnswerQuestion::where('user_id', $member->user_id)
            ->where('quiz_id', $quiz->id)
            ->where('is_correct', 1)
            ->count();
        
        // Update or create quiz result
        $quizResult = CourseQuizResult::updateOrCreate(
            [
                'user_id' => $member->user_id,
                'course_id' => $course->id,
                'quiz_id' => $quiz->id
            ],
            [
                'score' => $correctAnswersCount,
                'total_questions' => $quiz->questions->count(),
                'efficiency' => $quiz->questions->count() > 0 ? ($correctAnswersCount / $quiz->questions->count()) * 100 : 0
            ]
        );
    }
    
    /**
     * Calculate total score from assignments and quizzes
     */
    private function calculateTotalScore(Course $course, CourseMember $member)
    {
        // Get assignment scores
        $assignmentScore = 0;
        $assignments = $course->courseAssignments()->with(['answers' => function($query) use ($member) {
            $query->where('user_id', $member->user_id);
        }])->get();
        
        foreach ($assignments as $assignment) {
            $memberAnswers = $assignment->answers->where('user_id', $member->user_id);
            if ($memberAnswers->isNotEmpty()) {
                // Get the highest score for this assignment
                $highestScore = $memberAnswers->max('points');
                $assignmentScore += $highestScore ?? 0;
            }
        }
        
        // Get quiz scores
        $quizScore = CourseQuizResult::where('user_id', $member->user_id)
            ->where('course_id', $course->id)
            ->sum('score');
        
        // Update member's total achieved score
        $totalScore = $assignmentScore + $quizScore;
        $member->update([
            'achieved_score' => $totalScore
        ]);
    }

}
