<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    public $table = 'tests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'course_id',
        'duration',
        'minimum_percentage',
        'instruction',
        'permitted_attempt_number',
        'total_mark',
        'status'
    ];

    public const FILES = [
        'poster_url' => 'public/', // for display path view
    ];

    public const VIDEOS = [ // for remove video files
        'video_url' => 'public/',
    ];
    
    /* Связанные таблицы */
    public function course() {
        return $this->belongsTo(
            Course::class,
            'course_id'
        );
    }

    public function questions() {
        return $this->hasMany(
            TestQuestion::class,
            'test_id'
        );
    }

    /* Преобразование полей */

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
