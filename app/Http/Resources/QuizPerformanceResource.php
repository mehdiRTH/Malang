<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizPerformanceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success_rate'=>$this->success_rate,
            'answer_language'=>$this->answer_language->getDisplayedName(),
            'vocabulary_number'=>$this->vocabulary_number,
            'quiz_date'=>$this->quiz_date ?? 'Themed Quiz',
            'created_at'=>$this->created_at->format('Y-m-d H:m')
        ];
    }
}
