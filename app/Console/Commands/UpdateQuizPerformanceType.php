<?php

namespace App\Console\Commands;

use App\Models\QuizPerformance;
use Illuminate\Console\Command;

class UpdateQuizPerformanceType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:update-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update type column In quiz performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        QuizPerformance::where('type',null)->update([
            'type'=>'Vocabulary'
        ]);

        return $this->info('Quiz Performance Updated successfully');
    }
}
