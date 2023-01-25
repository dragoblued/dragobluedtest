<?php

namespace App;

use Illuminate\database\Eloquent\Model;

class Event extends Model
{
    public $table = 'events';

    protected $casts = [
        'langs' => 'array',
        'plan' => 'array',
        'program' => 'array',
        'promo_video_available_formats' => 'array',
        'address_coordinates' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'route',
        'name',
        'status',
        'is_model_visible',
        'order',
        'title',
        'sub_title',
        'subsign',
        'description',
        'duration',
        'langs',
        'plan',
        'program',
        'poster_url',
        'model_url',
        'collage_url',
        'promo_video_url',
        'promo_video_available_formats',

        'address',
        'address_building_name',
        'address_url',
        'address_coordinates',
        'address_howtoreach',

        /* Price */
        'actual_price',
        'discount_price',

        /* Statistics */
        'positive_votes_count',
        'negative_votes_count',
        'view_count',
        'bought_tickets_count'
    ];

    public const FILES = [
        'poster_url' => 'public/media/', // for display path view
        'model_url' => 'public/media/', // for display path view
        'collage_url' => 'public/media/', // for display path view
    ];

    public const VIDEOS = [
        'promo_video_url' => 'public/',
    ];
    /* Связанные таблицы */
    public function gallery() {
        return $this->belongsToMany(
            Gallery::class,
            'gallery_pivot',
            'event_id',
            'gallery_id'
        );
    }

    public function dates() {
        return $this->hasMany(
            Date::class,
            'event_id'
        )->orderBy('start');
    }

   public function promocodes()
   {
      return $this->morphMany(Promocode::class, 'subject');
   }

    /* Преобразование полей */
    /**
     * @var mixed
     */

    public function getTimeFieldAttribute()
    {
        return 1;
    }

   public function getLangStrAttribute() {
      return is_array($this->langs) ? implode(', ', $this->langs) : null;
   }

//    public function getDatesAttribute($value)
//    {
//        $arr = json_decode($value, true);
//        if (is_array($arr)) {
//            $groups = array();
//            foreach ($arr as $date) {
//                $groups[$date['year']][] = $date;
//            }
//            return $groups;
//        } else {
//            return null;
//        }
//    }

    /* Преобразование полей (save) */

    /* Преобразование полей */
    public function poster_url (string $sizer = null): string
    {
        if($sizer) $sizer .= '/';
        $folder = self::FILES['poster_url'];
        $folder = str_replace('public/', '', $folder);
        $src = "{$folder}{$sizer}{$this->poster_url}";
        return $src;
    }

    public function collage_url (string $sizer = null): string
    {
        if($sizer) $sizer .= '/';
        $folder = self::FILES['collage_url'];
        $folder = str_replace('public/', '', $folder);
        $src = "{$folder}{$sizer}{$this->collage_url}";
        return $src;
    }

//    public function setDateAttribute($value){
//        $this->attributes['date'] = (new Carbon($value))->format('Y-m-d H:m:s');
//
//    }
    /* Заготовки запросов */
}
