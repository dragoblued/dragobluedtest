<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    public $table = 'test_results';

    protected $casts = [
        'obtained_marks' => 'array',
        'test_started_timestamp' => 'datetime'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'test_id',
        'user_id',
        'answer_script',
        'obtained_marks',
        'max_mark',
        'max_mark_percent',
        'status',
        'test_started_timestamp',
        'attempt_number',
        'result'
    ];

    /* Связанные таблицы */
    public function test() {
        return $this->belongsTo(
            Test::class,
            'test_id'
        );
    }

    public function user() {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    /* Преобразование полей */

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
