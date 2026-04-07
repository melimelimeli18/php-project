<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('body');
            $table->enum('type', ['mcq', 'short_answer']);
            $table->foreignId('subject_id')->constrained('subjects');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('source', ['manual', 'bank', 'community', 'ai', 'previous_year'])
                  ->default('manual');
            $table->boolean('is_public')->default(false);
            $table->jsonb('keywords')->nullable();           // ["fotosintesis", "klorofil"]
            $table->integer('keyword_threshold')->nullable(); // min keywords to pass
            $table->jsonb('meta')->nullable();               // {"year": 2023, "topic": "..."}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
