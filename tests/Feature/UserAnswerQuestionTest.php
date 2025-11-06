<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\CourseMember;
use App\Models\CourseQuiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\UserAnswerQuestion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAnswerQuestionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Course $course;
    private CourseQuiz $quiz;
    private Question $question;
    private QuestionOption $correctOption;
    private QuestionOption $incorrectOption;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->course = Course::factory()->create();
        
        // Add user as course member
        CourseMember::factory()->create([
            'user_id' => $this->user->id,
            'course_id' => $this->course->id,
        ]);

        $this->quiz = CourseQuiz::factory()->create([
            'course_id' => $this->course->id,
        ]);

        $this->question = Question::factory()->create([
            'questionable_type' => 'App\Models\CourseQuiz',
            'questionable_id' => $this->quiz->id,
        ]);

        $this->correctOption = QuestionOption::factory()->create([
            'question_id' => $this->question->id,
        ]);

        $this->incorrectOption = QuestionOption::factory()->create([
            'question_id' => $this->question->id,
        ]);

        $this->question->update([
            'correct_option_id' => $this->correctOption->id,
        ]);
    }

    public function test_user_can_answer_question_for_first_time()
    {
        Sanctum::actingAs($this->user);

        $response = $this->post("/quizs/{$this->quiz->id}/questions/{$this->question->id}/answers", [
            'course_id' => $this->course->id,
            'answer_id' => $this->correctOption->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'บันทึกคำตอบเรียบร้อยแล้ว',
            ]);

        $this->assertDatabaseHas('user_answer_questions', [
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'quiz_id' => $this->quiz->id,
            'answer_id' => $this->correctOption->id,
        ]);
    }

    public function test_user_cannot_answer_same_question_twice()
    {
        Sanctum::actingAs($this->user);

        // First answer
        UserAnswerQuestion::factory()->create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'quiz_id' => $this->quiz->id,
            'answer_id' => $this->incorrectOption->id,
        ]);

        // Try to answer again
        $response = $this->post("/quizs/{$this->quiz->id}/questions/{$this->question->id}/answers", [
            'course_id' => $this->course->id,
            'answer_id' => $this->correctOption->id,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'คุณได้ตอบคำถามนี้ไปแล้ว',
            ]);
    }

    public function test_user_can_edit_existing_answer()
    {
        Sanctum::actingAs($this->user);

        // Create initial answer
        $existingAnswer = UserAnswerQuestion::factory()->create([
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'quiz_id' => $this->quiz->id,
            'answer_id' => $this->incorrectOption->id,
        ]);

        // Edit the answer
        $response = $this->patch("/quizs/{$this->quiz->id}/questions/{$this->question->id}/answers/{$existingAnswer->id}", [
            'course_id' => $this->course->id,
            'answer_id' => $this->correctOption->id,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'แก้ไขคำตอบเรียบร้อยแล้ว',
            ]);

        $this->assertDatabaseHas('user_answer_questions', [
            'id' => $existingAnswer->id,
            'user_id' => $this->user->id,
            'question_id' => $this->question->id,
            'quiz_id' => $this->quiz->id,
            'answer_id' => $this->correctOption->id,
        ]);
    }

    public function test_user_cannot_edit_another_users_answer()
    {
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($this->user);

        // Create answer by another user
        $anotherUserAnswer = UserAnswerQuestion::factory()->create([
            'user_id' => $anotherUser->id,
            'question_id' => $this->question->id,
            'quiz_id' => $this->quiz->id,
            'answer_id' => $this->incorrectOption->id,
        ]);

        // Try to edit another user's answer
        $response = $this->patch("/quizs/{$this->quiz->id}/questions/{$this->question->id}/answers/{$anotherUserAnswer->id}", [
            'course_id' => $this->course->id,
            'answer_id' => $this->correctOption->id,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'ไม่สามารถแก้ไขคำตอบนี้ได้',
            ]);
    }
}