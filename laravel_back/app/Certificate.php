<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    public $table = 'certificates';

    protected $casts = [
        'send_query' => 'boolean'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'test_id',
        'user_id',
        'file_url',
        'delivery_status',
        'delivery_address',
        'delivery_tracking_url',
    ];

    /* Связанные таблицы */
    public function course() {
        return $this->belongsTo(
            Course::class,
            'course_id'
        );
    }

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
