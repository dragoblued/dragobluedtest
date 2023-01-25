<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\ApiPasswordResetSuccess;
use App\PasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

class ApiResetPasswordController extends Controller
{
   /**
    * Find token password reset
    *
    * In table reset_passwords
    *
    * @param  string  $token
    * @return JsonResponse
    */
   public function reset(string $token)
   {
      $passwordReset = PasswordReset::where('token', $token)
         ->first();

      if (!$passwordReset)
         return redirect(config('app.site_url')."/auth/email?token=invalid&message=This password reset token is invalid.");

      if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {

         $passwordReset->delete();
         return redirect(config('app.site_url')."/auth/email?token=invalid&message=This password reset token is invalid.");
      }

      $redirectPath = "email=$passwordReset->email&token=$passwordReset->token";
      return redirect(config('app.site_url')."/auth/reset?$redirectPath");
   }

   /**
    * Update password
    *
    * @param  Request  $request
    * @return JsonResponse
    * @uses ApiPasswordResetSuccess - notify email
    */
   public function update(Request $request)
   {
      $request->validate([
         'email' => 'required|string|email',
         'password' => 'required|string|confirmed',
         'token' => 'required|string'
      ]);

      $passwordReset = PasswordReset::where([
         ['token', $request->token],
         ['email', $request->email]
      ])->first();

      if (!$passwordReset)
         return response()->json([
            'message' => 'This password reset token is invalid.'
         ], 404);

      $user = User::where('email', $passwordReset->email)->first();
      if (!$user) {
         return response()->json([
            'message' => 'We can\'t find an account with this email.'
         ], 404);
      }

      $user->password = bcrypt($request->password);

      $user->save();

      $passwordReset->delete();

      $user->notify(new ApiPasswordResetSuccess($passwordReset));

      return response()->json($user);
   }
}
