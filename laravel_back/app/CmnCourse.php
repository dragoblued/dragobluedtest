<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CmnCourse extends Model
{
    public $table = 'cmn-courses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'event_id',
        'route',
        'name',
        'title',
        'sub_title',
        'subsign',
        'description'
    ];

    /* Связанные таблицы */
    public function course ()
    {
        return $this->belongsTo(
            Course::class,
            'course_id',
            'id'
        );
    }

    public function event ()
    {
        return $this->belongsTo(
            Event::class,
            'event_id',
            'id'
        );
    }

    /* Преобразование полей */

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
