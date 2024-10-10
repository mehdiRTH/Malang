<?php

namespace App\Http\Resources;

use App\Models\Exam;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'weekNumber' => $this['weekNumber'],
            'startDate'  => $this['startDate'],
            'endDate'    => $this['endDate'],
            'vocabularies' => Vocabulary::whereDate('date','>=',$this['startDate'])->whereDate('date','<=',$this['endDate'])->count(),
            'exam' => auth()->user()->exams()->whereDate('start_date','>=',$this['startDate'])->whereDate('end_date','<=',$this['endDate'])->first()
        ];
    }
}
