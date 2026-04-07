<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->string('type')->default('mid_term')->change();
        });
        
        Schema::table('questions', function (Blueprint $table) {
            $table->string('type')->default('mcq')->change();
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->enum('type', ['mid_term', 'final_term'])->default('mid_term')->change();
        });
        
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', ['mcq', 'short_answer'])->default('mcq')->change();
        });
    }
};
