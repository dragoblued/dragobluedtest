<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserLesson extends Pivot
{
    public $table = 'user_lesson';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function lesson() {
        return $this->belongsTo(Lesson::class);
    }
}
