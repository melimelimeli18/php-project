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
        Schema::table('chapters', function (Blueprint $table) {
            $table->renameColumn('number', 'order');
            $table->renameColumn('title', 'name');
        });

        // Need to change the default value of the renamed 'order' column
        Schema::table('chapters', function (Blueprint $table) {
            $table->integer('order')->default(0)->change();
        });

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'chapter')) {
                $table->dropColumn('chapter');
            }
            $table->boolean('is_public')->default(true)->change();
        });
        
        \Illuminate\Support\Facades\DB::table('questions')->update(['is_public' => true]);

        Schema::table('quizzes', function (Blueprint $table) {
            $table->unique(['subject_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropUnique(['subject_id', 'type']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->change();
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->integer('order')->default(null)->change();
            $table->renameColumn('order', 'number');
            $table->renameColumn('name', 'title');
        });
    }
};
