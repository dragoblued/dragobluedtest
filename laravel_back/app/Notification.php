<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    public $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'is_new',
        'type',
        'name',
        'message_id'
    ];

    /* Связанные таблицы */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function message(): BelongsTo {
        return $this->belongsTo(Message::class)->with(['linkMessage', 'user']);
    }

    /* Преобразование полей */

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
