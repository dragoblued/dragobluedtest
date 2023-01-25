<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class SocialAuthController extends Controller
{
   public function login(Request $request): JsonResponse
   {
      if (!$request->email) {
         return response()->json([
            'message' => 'You must specify your email address first in your google|facebook account',
            'type' => 'warning'
         ], 403);
      }
      $request->validate([
         'email' => 'required|string|email',
         'fingerprint' => 'required|string'
      ]);
      $existUser = User::withTrashed()->where('email', $request->email)->first();

      if($existUser) {
         $user = $existUser;
         $user->active = true;
         $user->deleted_at = null;
         $user->save();
      } else {
         $user = new User;
         $user->active = true;
         $user->name = $request->name;
         $user->email = $request->email;
         $user->social_id = $request->id;
         $user->password = md5(rand(1,10000));
//            $user->activation_token = str_random(60);
         $user->role_id = 2;
         $user->save();
      }

      $devices = $user->device_ids ?? [];
      if (!in_array($request->fingerprint, $devices) && count($devices) >= 3) {
         return response()->json([
            'message' => 'You reached the limit of 3 authorized devices for one account. Please, contact support service if you want to change connected device for this account',
            'type' => 'warning'
         ], 403);
      } elseif (!in_array($request->fingerprint, $devices) && count($devices) < 3) {
         array_push($devices, $request->fingerprint);
         $user->device_ids = $devices;
         $user->save();
      }

      $tokenResult = $user->createToken('Personal Access Token');
      $token = $tokenResult->token;
      $token->expires_at = Carbon::now()->addDay();

      if ($request->remember_me) {
         $token->expires_at = Carbon::now()->addWeeks(1);
      }

      $token->save();
      return response()->json([
         'access_token' => $tokenResult->accessToken,
         'token_type' => 'Bearer',
         'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
      ]);
   }

}
