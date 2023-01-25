<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserCourse extends Pivot
{
   public $table = 'user_course';

   public function user() {
      return $this->belongsTo(User::class);
   }

   public function course() {
      return $this->belongsTo(Course::class);
   }

   public function invoice() {
      return $this->belongsTo(Invoice::class);
   }

}
