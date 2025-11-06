<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseQuizResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            
            'user' => $this->whenLoaded('user', function () {
                return new UserResource($this->user);
            }),

            'course_id' => $this->course_id,
            'quiz_id' => $this->quiz_id,
            'score' => $this->score,
            'percentage' => $this->percentage,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'duration' => $this->duration,
            'attempted_questions' => $this->attempted_questions,
            'correct_answers' => $this->correct_answers,
            'incorrect_answers' => $this->incorrect_answers,
            'skipped_questions' => $this->skipped_questions,
            'passed' => $this->passed ?? null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships (only include if loaded)
            // 'user' => $this->when(isset($this->user) && $this->user, function () {
            //     return [
            //         'id' => $this->user->id,
            //         'name' => $this->user->name,
            //         'email' => $this->user->email,
            //     ];
            // }),
            // Relationships - commented out to avoid errors
            // 'user' => new UserResource($this->user),
            // 'course' => new CourseResource($this->course),
            // 'quiz' => isset($this->quiz) && $this->quiz ? [
            //     'id' => $this->quiz->id,
            //     'title' => $this->quiz->title,
            //     'total_score' => $this->quiz->total_score,
            //     'passing_score' => $this->quiz->passing_score,
            // ] : null,
            
            // Computed properties
            'status' => $this->getStatus(),
            'grade' => $this->getGrade(),
            'time_taken' => $this->getTimeTaken(),
            'performance_level' => $this->getPerformanceLevel(),
        ];
    }
    
    /**
     * Get the status of the quiz result
     */
    private function getStatus(): string
    {
        if ($this->completed_at && $this->completed_at !== '') {
            return ($this->passed ?? false) ? 'passed' : 'failed';
        }
        
        return 'in_progress';
    }
    
    /**
     * Get the grade based on percentage
     */
    private function getGrade(): string
    {
        if (!$this->percentage) {
            return 'N/A';
        }
        
        if ($this->percentage >= 90) return 'A';
        if ($this->percentage >= 80) return 'B';
        if ($this->percentage >= 70) return 'C';
        if ($this->percentage >= 60) return 'D';
        
        return 'F';
    }
    
    /**
     * Get formatted time taken
     */
    private function getTimeTaken(): string
    {
        if (!$this->duration) {
            return 'N/A';
        }
        
        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;
        
        $timeParts = [];
        
        if ($hours > 0) {
            $timeParts[] = "{$hours}h";
        }
        
        if ($minutes > 0) {
            $timeParts[] = "{$minutes}m";
        }
        
        if ($seconds > 0 || empty($timeParts)) {
            $timeParts[] = "{$seconds}s";
        }
        
        return implode(' ', $timeParts);
    }
    
    /**
     * Get performance level based on percentage
     */
    private function getPerformanceLevel(): string
    {
        if (!$this->percentage) {
            return 'Not Attempted';
        }
        
        if ($this->percentage >= 90) return 'Excellent';
        if ($this->percentage >= 80) return 'Good';
        if ($this->percentage >= 70) return 'Average';
        if ($this->percentage >= 60) return 'Below Average';
        
        return 'Poor';
    }
}