<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Ticket extends Pivot
{
   public $table = 'tickets';

   protected $casts = [
      'recipient_persons' => 'array',
   ];

   public function user() {
      return $this->belongsTo(User::class);
   }

   public function date() {
      return $this->belongsTo(Date::class)->with(['event']);
   }

   public function invoice() {
      return $this->belongsTo(Invoice::class);
   }

//   public function event() {
//      return $this->hasOneThrough(
//         Event::class,
//         Date::class,
//         'event_id',
//         'id',
//         null
//      );
//   }
}
