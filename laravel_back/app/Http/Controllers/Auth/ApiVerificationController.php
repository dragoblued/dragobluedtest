<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;

class ApiVerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::withTrashed()->where('activation_token', $token)->first();

        if (!$user) {
           return response()->json([
              'message' => 'This activation token is invalid.'
           ], 403);
        }

//        $user->active = true;
        $user->activation_token = null;
        $user->deleted_at = null;
        $user->save();

        return $user;
    }
}
