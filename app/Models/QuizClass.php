<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class QuizClass extends Pivot
{
    protected $table = 'quiz_class';

    protected static function booted()
    {
        static::creating(function ($pivot) {
            if (! $pivot->assigned_by) {
                $pivot->assigned_by = auth()->id() ?? 1; // Fallback to 1 if console
            }
        });
    }
}
