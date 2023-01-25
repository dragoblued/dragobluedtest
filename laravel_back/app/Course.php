<?php

namespace App;

use App\Classes\UpdateTotalCount;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
   public $table = 'courses';

   protected $casts = [
      'lang' => 'array',
      'promo_video_available_formats' => 'array'
   ];

   /**
    * The attributes that are mass assignable.
    *
    * @var array
    */

   protected $fillable = [
      'user_creator_id',
      'route',
      'name',
      'status',
      'lang',
      'is_model_visible',
      'order',
      'title',
      'sub_title',
      'subsign',
      'description',
      'faq',
      'tags',
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
      /**/
      'topics_count',
      'lessons_count',
      'total_lessons_duration',
      'status',
      'refund_policy',
      /* Students related info */
      'what_will_students_learn',
      'target_students',
      'requirements',
      /* Price */
      'actual_price',
      'discount_price',
      /* Statistics */
      'positive_votes_count',
      'positive_votes_count',
      'view_count',
      'purchase_count'
   ];


   /* Image */
   public const FILES = [
      'poster_url' => 'public/', // for display path view
   ];

   public const VIDEOS = [
      'promo_video_url' => 'public/',
   ];


   /* Связанные таблицы */
   public function lessons()
   {
      return $this->hasManyThrough(
         Lesson::class,
         Topic::class,
         'course_id',
         'topic_id'
      )->orderBy('order');
   }

   public function topics()
   {
      return $this->hasMany(
         Topic::class,
         'course_id'
      )->with(['lessons'])->orderBy('order');
   }

    public function topicsSimple()
    {
        return $this->hasMany(
            Topic::class,
            'course_id'
        )->orderBy('order');
    }


   public function test()
   {
      return $this->hasOne(
         Test::class,
         'course_id'
      );
   }

   public function user_creator ()
   {
      return $this->belongsTo(
         User::class,
         'user_creator_id',
         'id'
      );
   }

   public function promocodes()
   {
      return $this->morphMany(Promocode::class, 'subject');
   }

   /* Преобразование полей */
   public function getLangStrAttribute() {
      return is_array($this->lang) ? implode(', ', $this->lang) : null;
   }

   public function getCalcTotalDurationAttribute() {
      return (new UpdateTotalCount())->getCourseTotalDuration($this);
   }

   /* Преобразование полей (save) */

   /* Заготовки запросов */

   /* Преобразование полей */
   public function poster_url (string $sizer = null): string
   {
      if($sizer) $sizer .= '/';
      $folder = self::FILES['poster_url'];
      $folder = str_replace('public/', '', $folder);
      $src = "{$folder}{$sizer}{$this->poster_url}";
      return $src;
   }
}
