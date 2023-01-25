<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserTopic extends Pivot
{
   public $table = 'user_topic';

   public function user() {
      return $this->belongsTo(User::class);
   }

   public function topic() {
      return $this->belongsTo(Topic::class);
   }

   public function invoice() {
      return $this->belongsTo(Invoice::class);
   }
}
