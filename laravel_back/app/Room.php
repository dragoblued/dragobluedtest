<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Log;

class Room extends Model
{
    public $table = 'rooms';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'subject_id',
        'subject_type',
        'creator_id'
    ];

    public $totalMessagesCount = 0;
    public $newMessagesCount = 0;
    public $lastMessage;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
//      $this->calcNewMessagesCount();
    }

    public function calcNewMessagesCount()
    {
        $messages = $this->messages()->orderBy('id', 'desc')->get();
        $this->totalMessagesCount += count($messages);
        $this->lastMessage = $this->totalMessagesCount > 0 ? $messages[0] : null;
        $newMessages = $messages->filter(function ($message) {
            return $message->status === 0;
        });
        $this->newMessagesCount += count($newMessages);
    }

    /* Связанные таблицы */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator (): belongsTo
    {
        return $this->belongsTo(
            User::class,
            'creator_id',
            'id'
        );
    }

    public function users (): belongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_room',
            'room_id',
            'user_id'
        );
    }

    public function simpledUsers (): belongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'user_room',
            'room_id',
            'user_id'
        )->select(['users.id', 'active', 'avatar_url', 'email', 'name', 'surname', 'role_id']);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(
            Message::class,
            'room_id'
        )->with(['linkMessage']);
    }

    public function newMessages(): HasMany
    {
        return $this->hasMany(
            Message::class,
            'room_id'
        )->where('status', 0)->with(['linkMessage']);
    }

    public function selectedMessages(): HasMany
    {
        return $this->hasMany(
            Message::class,
            'room_id'
        )->where('status', 2)->with(['linkMessage']);
    }

    /* Преобразование полей */

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
