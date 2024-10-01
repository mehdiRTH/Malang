<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quiz_performances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('success_rate');
            $table->string('answer_language');
            $table->bigInteger('vocabulary_number')->default(10);
            $table->date('quiz_date')->nullable();
            $table->foreignId('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_performance');
    }
};
