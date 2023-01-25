<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    public $table = 'feedback';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'text',
        'url_from'
    ];

    /* Связанные таблицы */

    /* Преобразование полей */

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
