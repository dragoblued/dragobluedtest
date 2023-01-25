<?php

namespace App;

use App\Classes\UpdateTotalCount;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
   public $table = 'topics';

   protected $casts = [
      'lang' => 'array',
      'promo_video_available_formats' => 'array',
      'video_available_formats' => 'array'
   ];

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */

//    protected $casts = [
//        'promo_video_available_formats' => 'array',
//        'video_available_formats' => 'array'
//    ];

   protected $fillable = [
      'course_id',
      'user_creator_id',
      'route',
      'name',
      'status',
      'lang',
      'order',
      'title',
      'sub_title',
      'description',
      /* Image */
      'poster_url',
      /* Promo video */
      'promo_video_url',
      'promo_video_original_name',
      'promo_video_duration',
      'promo_video_available_formats',
      /* Success video convert */
      'converted',
      'converted_at',
      /* Count, duration */
      'lessons_count',
      'total_lessons_duration',
      /* Price */
      'is_free',
      'actual_price',
      'discount_price',
      /* Statistics */
      'positive_votes_count',
      'positive_votes_count',
      'view_count',
      'purchase_count'
   ];

   public const FILES = [
      'poster_url' => 'public/', // for display path view
   ];

   public const VIDEOS = [
      'promo_video_url' => 'public/',
   ];

   /* Связанные таблицы */
   public function course ()
   {
      return $this->belongsTo(
         Course::class,
         'course_id',
         'id'
      );
   }

   public function lessons ()
   {
      return $this->hasMany(
         Lesson::class,
         'topic_id'
      )->orderBy('order');
   }

   public function user_creator ()
   {
      return $this->belongsTo(
         User::class,
         'user_creator_id',
         'id'
      );
   }

   /* Преобразование полей */
   public function poster_url (string $sizer = null): string
   {
      if($sizer) $sizer .= '/';
      $folder = self::FILES['poster_url'];
      $folder = str_replace('public/', '', $folder);
      $src = "{$folder}{$sizer}{$this->poster_url}";
      return $src;
   }

   public function getLangStrAttribute() {
      return is_array($this->lang) ? implode(', ', $this->lang) : null;
   }

   public function getCalcTotalDurationAttribute() {
      return (new UpdateTotalCount())->getTopicTotalDuration($this);
   }
   /* Преобразование полей (save) */

   /* Заготовки запросов */

}
