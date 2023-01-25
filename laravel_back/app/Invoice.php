<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
   public $table = 'invoices';

   protected $casts = [
      'basket' => 'array',
      'session_object' => 'array'
   ];

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
      'user_id',
      'method',
      'session_id',
      'session_object',
      'basket',
      'price',
      'currency',
       /* 0 - unpaid
         *  1 - paid and successfully processed
         *  3 - paid but process errored
         */
      'state',
      'receipt_url',
      'additional_data',
      'paid_as_company',
      'company_invoice_url'
   ];

   /* Связанные таблицы */
    public function user() {
        return $this->belongsTo(User::class);
    }

   /* Преобразование полей */

   /* Преобразование полей (save) */

   /* Заготовки запросов */
}
