<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrammarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if(isset($this->vocabulary_grammar['nl']))
        {
            return [
                'name'=>$this->name,
                'nl'=>$this->vocabulary_grammar['nl'],
                'answer'=>null
            ];
        }

        return [];

    }
}
