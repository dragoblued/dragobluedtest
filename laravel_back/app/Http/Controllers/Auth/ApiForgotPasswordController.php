<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\ApiPasswordResetRequest;
use App\PasswordReset;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiForgotPasswordController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  Request  $request
     * @return JsonResponse
     * @uses ApiPasswordResetRequest - notify email
     */
    public function email(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

       $deletedUser = User::onlyTrashed()->where('email', $request->email)->first();
       if ($deletedUser) {
          return response()->json([
             'message' => 'The account linked to this email was deleted. Please register again with this email if you want recover it.',
             'type' => 'warning'
          ], 403);
       }

        $user = User::where('email', $request->email)->first();

        if (!$user)
            return response()->json([
                'message' => 'We can\'t find an account with this email.'
            ], 404);

        $passwordReset = PasswordReset::updateOrCreate(['email' => $user->email], [
            'email' => $user->email,
            'token' => str_random(60)
        ]);

        if ($user && $passwordReset)
            $user->notify(new ApiPasswordResetRequest($passwordReset->token));

        return response()->json([
            'message' => 'Password reset link has been sent! Check your email'
        ]);
    }
}
