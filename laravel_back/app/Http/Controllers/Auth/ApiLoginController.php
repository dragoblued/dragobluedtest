<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Log;

class ApiLoginController extends Controller
{
   // TODO: maybe create auth with login, now only email
   /**
    * Create user deactivate and send notification to activate account user
    *
    * Form login: email, password
    *
    * @param  Request  $request
    * @return JsonResponse
    */
   public function login(Request $request)
   {
      $request->validate([
         'email' => 'required|string|email',
         'password' => 'required|string',
         'remember_me' => 'boolean',
         'fingerprint' => 'required|string'
      ]);

      $credentials = request(['email', 'password']);
//        $credentials['active'] = 1;
      $credentials['deleted_at'] = null;

      $deletedUser = User::onlyTrashed()->where('email', $request->email)->first();
      if ($deletedUser) {
         if (!is_null($deletedUser->activation_token)) {
            return response()->json([
               'message' => 'You must verify your email address first. Please, check your email for recovery link',
               'type' => 'warning'
            ], 403);
         }
      }

      if(!Auth::attempt($credentials))
         return response()->json([
            'message' => 'Incorrect credentials'
         ], 401);

      $user = $request->user();

      if(!is_null($user->activation_token)) {
         return response()->json([
            'message' => 'You must verify your email address first. Please, check your email for verify link',
            'type' => 'warning'
         ], 403);
      }

      /* Check device relative uniqueness */
      $unlimitedUsers = [1,2,3,4];
      if (!in_array($user->id, $unlimitedUsers)) {
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

   public function logout (Request $request)
   {

      $request->user()->token()->revoke();

      return response()->json([
         'message' => 'Successfully logged out'
      ]);
   }
}

///* Check device relative uniqueness */
//$devices = $user->device_ids ?? [];
//$requestFingerprint = explode(',', $request->fingerprint)[0];
//$requestBrowserId = explode(',', $request->fingerprint)[1] ?? null;
//$match = false;
//$matchIdx = 0;
//foreach ($devices as $idx => $device) {
//   $fingerprint = explode(',', $device)[0];
//   $browserId = explode(',', $device)[1] ?? null;
//   if ($fingerprint === $requestFingerprint || $browserId === $requestBrowserId) {
//      $match = true;
//      $matchIdx = $idx;
//      break;
//   }
//}
//if (!$match && count($devices) >= 3) {
//   return response()->json([
//      'message' => 'You reached the limit of 3 authorized devices for one account. Please, contact support service if you want to change connected device for this account',
//      'type' => 'warning'
//   ], 403);
//} elseif (!$match && count($devices) < 3) {
//   array_push($devices, $request->fingerprint);
//} else {
//   $devices[$matchIdx] = $request->fingerprint;
//}
//$user->device_ids = $devices;
//$user->save();
