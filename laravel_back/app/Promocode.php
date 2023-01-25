<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Promocode extends Model
{
   public $table = 'promocodes';

   protected $casts = [
      'start_at' => 'date',
      'end_at' => 'date'
   ];

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */

   protected $fillable = [
      'code',
      'discount_type',
      'discount',
      'start_at',
      'end_at',
      'usage_limit',
      'usage_count',
      'user_id',
      'subject_id',
      'subject_type',
      'group_id'
   ];

   /* Связанные таблицы */
   public function group(): BelongsTo
   {
      return $this->belongsTo(Group::class);
   }

   public function subject(): MorphTo
   {
      return $this->morphTo();
   }

   /* Преобразование полей */

   /* Преобразование полей (save) */

   /* Заготовки запросов */
}
