<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamenQuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this[0]);
        return [
            '0'=>QuizResource::collection($this[0]),
            '1'=>QuizResource::collection($this[1]),
            '2'=>QuizResource::collection($this[2]),
            '3'=>QuizResource::collection($this[3]),
        ];
    }
}
