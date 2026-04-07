<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_class', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamp('assigned_at')->useCurrent();
            $table->unique(['quiz_id', 'class_id']);
            // note: using primary(['quiz_id', 'class_id']) with an auto-incrementing id is invalid. We use unique index instead.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_class');
    }
};
