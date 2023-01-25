<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestQuestion extends Model
{
    public $table = 'test_questions';

    public const FILES = [
        'files' => 'public/'
    ];

    protected $casts = [
        'correct_answers' => 'array'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'correct_answers'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'test_id',
        'type',
        'title',
        'number_of_options',
        'options',
        'correct_answers',
        'mark'
    ];

    /* Связанные таблицы */
    public function test() {
        return $this->belongsTo(
            Test::class,
            'test_id'
        );
    }

    /* Преобразование полей */

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
