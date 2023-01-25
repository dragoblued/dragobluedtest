<?php

use App\Stream;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/
if (request()->hasHeader('authorization')){
   Broadcast::routes(['prefix' => 'api', 'middleware' => ['auth:api']]);
} else {
   Broadcast::routes(['middleware' => ['web', 'auth']]);
}


Broadcast::channel('App.User.{id}', function ($user, $id) {
   Log::debug('BROADCAST USER');
   return (int) $user->id === (int) $id;
});

// Broadcast::channel('room.{room_id}', function ($user, $room_id) {
// 	if($user->rooms->contains($room_id)){
// 		return $user->name;
// 	}
// });

Broadcast::channel('room.{room_id}', function ($user, $room_id) {
   Log::debug('BROADCAST ROOM');
   Log::debug($room_id);
   Log::debug($user->id);
   if (isset($user)) {
      if ($user->role_id === 1 || $user->rooms->contains($room_id)) {
         return true;
      }
   }
   return false;
});

Broadcast::channel('stream.{stream_name}.{stream_key}', function ($user, $stream_name, $stream_key) {
    Log::debug('BROADCAST STREAM');
    Log::debug($stream_name);
    Log::debug($stream_key);
    $stream = Stream::where([
        ['name', $stream_name],
        ['key', $stream_key],
    ])->first();
    if (is_null($stream)) {
        return false;
    } else {
        $allowed = $stream->allowed_users ?? [];
        $banned = $stream->banned_users ?? [];
        if (in_array($user->id, $banned)) {
            return false;
        }
        if ($user->role_id === 1) {
            return ['id' => $user->id, 'name' => $user->name.($user->surname ? ' '.$user->surname : ''), 'email' => $user->email, 'avatar_url' => $user->avatar_url];
        }
        if (count($allowed) > 0) {
            if (!in_array($user->id, $allowed)) {
                return false;
            }
        }
        return ['id' => $user->id, 'name' => $user->name.($user->surname ? ' '.$user->surname : ''), 'email' => $user->email, 'avatar_url' => $user->avatar_url];
    }
});
