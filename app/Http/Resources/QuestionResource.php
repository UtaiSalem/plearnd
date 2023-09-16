<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\UserAnswerQuestion;
use App\Http\Resources\UserResource;
use App\Http\Resources\QuestionOptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'creator'           => new UserResource($this->user),
            'questionable_id'   => $this->questionable_id,
            'questionable_type' => $this->questionable_type,
            'text'              => $this->text,
            'type'              => $this->type,
            'options'           => QuestionOptionResource::collection($this->options), // Assuming it's a JSON field
            'explanation'       => $this->explanation,
            'difficulty_level'  => $this->difficulty_level,
            'time_limit'        => $this->time_limit,
            'points'            => $this->points,
            'position'          => $this->position,
            'tags'              => $this->tags,
            'isAnsweredByAuth'  => $this->checkIsAnsweredByAuth($this->id, auth()->id()),
            'images'            => $this->images,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }

    public function checkIsAnsweredByAuth($q_id, $auth_id)
    {
        $result = UserAnswerQuestion::where('user_id',$auth_id)->where('question_id', $q_id)->first();
        return $result->answer_id ?? null;
    }
}
