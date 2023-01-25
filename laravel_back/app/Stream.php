<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Stream extends Model
{
    public $table = 'streams';

    protected $casts = [
        'video_available_formats' => 'array',
        'allowed_users' => 'array',
        'banned_users' => 'array'
    ];

    protected $hidden = [
        'key'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'key',
        'lang',
        'title',
        'sub_title',
        'description',
        'broadcaster_id',

        /* 0 - pending
           1 - awaiting broadcaster
           2 - broadcasting
           3 - broadcasting ended
           4 - broadcasting errored
        */
        'status',
        'is_expired',
        'allowed_users',
        'banned_users',

        'appointment_datetime',
        'start_at',
        'end_at',

        /* Poster */
        'poster_url',

        /* Recorded video */
        'recorded_video_url',
        'recorded_video_original_name',
        'recorded_video_duration',
        'recorded_video_available_formats',
        'recorded_video_type',
        'recorded_video_size',

        /* 0 - video not uploaded
           1 - video mp4 converted
           2 - video hls converted
           3 - video is converting
           4 - convertation errored
        */
        'recorded_video_converted',
        'recorded_video_converted_at',

        /* Price */
        'is_free',
        'actual_price',
        'discount_price',
        'seats_total',
        'seats_vacant',
        'seats_booked',
        'seats_purchased',

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
        'recorded_video_url' => 'public/',
    ];

    /* Связанные таблицы */
    public function user_creator ()
    {
        return $this->belongsTo(
            User::class,
            'user_creator_id',
            'id'
        );
    }

    public function room(): MorphOne
    {
        return $this->morphOne(Room::class, 'subject');
    }

    /* Преобразование полей */
}
