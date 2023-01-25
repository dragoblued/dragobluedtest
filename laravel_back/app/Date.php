<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    public $table = 'dates';

    protected $fillable = [
        'event_id',
        'year',
        'start',
        'end',
        'lang',
        'seats_total',
        'seats_vacant',
        'seats_booked',
        'seats_purchased',
        'is_expired'
    ];

    /* Связанные таблицы */
    public function event() {
        return $this->belongsTo(
            Event::class,
            'event_id'
        );
    }

   public function users() {
      return $this->belongsToMany(
         User::class,
         'tickets',
         'date_id',
         'user_id'
      );
   }

    /* Преобразование полей */

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
