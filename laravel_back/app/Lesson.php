<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    public $table = 'lessons';

    protected $casts = [
        'promo_video_available_formats' => 'array',
        'video_available_formats' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'topic_id',
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
        /* Full video */
        'video_url',
        'video_original_name',
        'video_duration',
        'video_available_formats',
        'video_type',
        'video_size',
        /* Promo video */
        'promo_video_url',
        'promo_video_original_name',
        'promo_video_duration',
        'promo_video_available_formats',
        'promo_video_size',
        /* Success video convert */
        'converted',
        'converted_at',
        'converting_progress',
        /* Price */
        'is_free',
        /* Statistics */
        'positive_votes_count',
        'negative_votes_count',
        'purchase_count',
        'view_count'
    ];

    public const FILES = [
        'poster_url' => 'public/', // for display path view
    ];

    public const VIDEOS = [ // for remove video files
        'video_url' => 'public/',
    ];

    /* Связанные таблицы */
    public function topic ()
    {
        return $this->belongsTo(
            Topic::class,
            'topic_id',
            'id'
        )->with('course');
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

   public function user_lesson()
   {
      return $this->belongsTo(UserLesson::class, 'id', 'lesson_id');
   }
   
    /* Преобразование полей */
//    public function poster_url (string $sizer = null): string
//    {
//        if($sizer) $sizer .= '/';
//        $folder = self::FILES['poster_url'];
//        $folder = str_replace('public/', '', $folder);
//        $src = "{$folder}{$sizer}{$this->poster_url}";
//        return $src;
//    }

//    public function promo_video_url (string $sizer = null): string
//    {
//        if($sizer) $sizer .= '/';
//        $folder = self::VIDEOS['promo_video_url'];
//        $folder = str_replace('public/', '', $folder);
//        $src = "{$folder}{$sizer}{$this->promo_video_url}";
//        return $src;
//    }
}
