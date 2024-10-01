<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if(isset($this['translations']['nl']))
        {
            return [
                'nl'=>$this['translations']['nl'],
                'en'=>$this['translations']['en'],
                'answer'=>null
            ];
        }

        return [];


    }
}
