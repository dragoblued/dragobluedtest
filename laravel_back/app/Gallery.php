<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    public $table = 'gallery';

    protected $casts = [
        'available_formats' => 'array'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'mime_type',
        'name',
        'url',
        'poster_url',
        'available_formats',
        'converted'
    ];

    public const FILES = [
        'url' => 'public/',
        'poster_url' => 'public/' // for display path view
    ];

    public const VIDEOS = [
        'url' => 'public/',
    ];

    /* Связанные таблицы */

    /* Преобразование полей */
//    public function url (string $sizer = null): string
//    {
//        if($sizer) $sizer .= '/';
//        $folder = self::FILES['url'];
//        $folder = str_replace('public/', '', $folder);
//        $src = "{$folder}{$sizer}{$this->url}";
//        return $src;
//    }
//
//    public function poster_url (string $sizer = null): string
//    {
//        if($sizer) $sizer .= '/';
//        $folder = self::FILES['poster_url'];
//        $folder = str_replace('public/', '', $folder);
//        $src = "{$folder}{$sizer}{$this->poster_url}";
//        return $src;
//    }
    /* Преобразование полей (save) */

    /* Заготовки запросов */

}
