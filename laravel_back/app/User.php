<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Hash;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    public $table = 'users';

    use HasApiTokens, Notifiable, SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'active',
        'avatar_url',
        'login',
        'name',
        'surname',
        'middle_name',
        'role_id',
        'email',
        'google_id',
        'facebook_id',
        'phone',
        'address',
        'zip',
        'need_company',
        'company_info',
        'password',
        'activation_token'
    ];

    public const FILES = [
        'avatar_url' => 'public/'
    ];

    public const VIDEOS = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
        'activation_token',
        'device_ids'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'device_ids' => 'array',
        'company_info' => 'array'
    ];

    public $newMessagesCount = 0;
    public $lastMessage;
    public $lastMessageDate;


    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function freshMessagesState()
    {
        foreach ($this->rooms()->get() as $room) {
            $room->calcNewMessagesCount();
            $this->newMessagesCount += $room->newMessagesCount;
            if ($room->lastMessage) {
                if ($this->lastMessage) {
                    if ($this->lastMessage->created_at < $room->lastMessage->created_at) {
                        $this->lastMessage = $room->lastMessage;
                        $this->lastMessageDate = $room->lastMessage->created_at;
                    }
                } else {
                    $this->lastMessage = $room->lastMessage;
                    $this->lastMessageDate = $room->lastMessage->created_at;
                }
            }
        }
    }

    public function hasPermissions($requiredPermissions) {
        if (is_array($requiredPermissions)) {
            $matchCount = 0;
            $mustMatchNum = sizeof($requiredPermissions);
            foreach ($requiredPermissions as $reqPerm) {
                foreach ($this->role->permissions as $permission) {
                    if(mb_strtolower($permission->name) == mb_strtolower($reqPerm)) {
                        $matchCount++;
                    }
                }
            }
            if ($matchCount === $mustMatchNum) {
                return true;
            }
        } else {
            foreach ($this->role->permissions as $permission) {
                if(mb_strtolower($permission->name) == mb_strtolower($requiredPermissions)) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    public function hasGroups($requiredGroups) {
        if (is_array($requiredGroups)) {
            $matchCount = 0;
            $mustMatchNum = sizeof($requiredGroups);
            foreach ($requiredGroups as $reqGroup) {
                foreach ($this->groups as $group) {
                    if(mb_strtolower($group->value) == mb_strtolower($reqGroup)) {
                        $matchCount++;
                    }
                }
            }
            if ($matchCount === $mustMatchNum) {
                return true;
            }
        } else {
            foreach ($this->groups as $group) {
                if(mb_strtolower($group->value) == mb_strtolower($requiredGroups)) {
                    return true;
                    break;
                }
            }
        }
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    /* Связанные таблицы */
    public function role()
    {
        return $this->belongsTo(
            Role::class,
            'role_id',
            'id'
        )->with(['permissions']);
    }

//    public function permissions()
//    {
//        return $this->hasManyThrough(
//            Permission::class,
//            Role::class,
//            'role_id',
//            'permission_id'
//        );
//    }

    public function groups ()
    {
        return $this->belongsToMany(
            Group::class,
            'user_group',
            'user_id',
            'group_id'
        );
    }

    public function rooms ()
    {
        return $this->belongsToMany(
            Room::class,
            'user_room',
            'user_id',
            'room_id'
        );
    }

    public function lessonRooms ()
    {
        return $this->belongsToMany(
            Room::class,
            'user_room',
            'user_id',
            'room_id'
        )->where('subject_type', 'App\Lesson');
    }

    public function streamRooms ()
    {
        return $this->belongsToMany(
            Room::class,
            'user_room',
            'user_id',
            'room_id'
        )->where('subject_type', 'App\Stream');
    }

    public function courses ()
    {
        return $this->belongsToMany(
            Course::class,
            'user_course',
            'user_id',
            'course_id'
        )->withPivot('lessons_view_count', 'is_purchased');
    }

    public function topics ()
    {
        return $this->belongsToMany(
            Topic::class,
            'user_topic',
            'user_id',
            'topic_id'
        )->withPivot('lessons_view_count', 'is_purchased');
    }

    public function lessons ()
    {
        return $this->belongsToMany(
            Lesson::class,
            'user_lesson',
            'user_id',
            'lesson_id'
        )->withPivot('current_timing', 'current_timing_percent', 'is_viewed', 'is_purchased');
    }

    public function dates ()
    {
        return $this->belongsToMany(
            Date::class,
            'tickets',
            'user_id',
            'date_id'
        )->withPivot('id', 'count', 'is_purchased', 'is_expired', 'is_canceled', 'is_reminded', 'recipient_persons')
            ->orderBy('pivot_id', 'desc');
    }

    public function promoCodes()
    {
        return $this->belongsToMany(
            Promocode::class,
            'user_promocode',
            'user_id',
            'promocode_id'
        )->withPivot('applied_count');
    }

    public function notifications()
    {
        return $this->hasMany(
            Notification::class,
            'user_id'
        )->with(['message'])->orderBy('id', 'desc');
    }

    public function progress() {
        return (object) [
            'courses' => UserCourse::where('user_id', $this->id)->orderBy('course_id')->get(),
            'topics'  => UserTopic::where('user_id', $this->id)->orderBy('topic_id')->get(),
            'lessons' => UserLesson::where('user_id', $this->id)->orderBy('lesson_id')->get(),
            'tickets' => Ticket::where([
                ['user_id', $this->id],
                ['is_canceled', '!=', 1]
            ])->get(),
            'tests'   => TestResult::where('user_id', $this->id)->get(),
            'certificates' => Certificate::where('user_id', $this->id)->with(['course'])->get(),
            'notifications' => $this->notifications
        ];
    }

    public function user_lessons()
    {
      return $this->hasMany(UserLesson::class, 'user_id', 'id');
    }
    
    /* Преобразование полей */

    /* Преобразование полей (save) */
    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
