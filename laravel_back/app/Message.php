<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    public $table = 'messages';

      use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'room_id',
        'user_id',
        'link',
        'text',
        /* 0 - new
           1 - viewed
           2 - marked
        */
        'status'
    ];

   protected $appends = [
      'user_avatar_url',
      'user_name'
   ];

    /* Связанные таблицы */
    public function room() {
        return $this->belongsTo(Room::class);
    }

    public function user() {
        return $this->belongsTo(User::class)
            ->select(['users.id', 'active', 'avatar_url', 'email', 'name', 'surname', 'role_id']);
    }

    public function linkMessage() {
        return $this->belongsTo(
            Message::class,
            'link'
        )->with(['user']);
    }

   public function attached() {
      return $this->hasMany(
         Message::class,
         'link'
      );
   }

    /* Преобразование полей */
   public function getUserAvatarUrlAttribute() {
      $user = User::find($this->user_id);
      return $user ? $user->avatar_url : null;
   }

   public function getUserNameAttribute() {
      $user = User::find($this->user_id);
      return $user ? ($user->name ? "{$user->name} {$user->surname}" : $user->email) : null;
   }

    /* Преобразование полей (save) */

    /* Заготовки запросов */
}
