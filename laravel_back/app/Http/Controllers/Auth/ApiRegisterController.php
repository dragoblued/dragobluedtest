<?php

namespace App\Http\Controllers\Auth;

use App\Notifications\ApiEmailVerifyAccount;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiRegisterController extends Controller
{
    /**
     * Create user deactivate and send notification to activate account user
     *
     * Form registration: email, password
     *
     * @param  Request  $request
     * @return JsonResponse
     * @uses ApiEmailVerifyAccount - notify email
     */
    public function register (Request $request)
    {
        $request->validate([
            'name' => 'nullable|string',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $currentUser = User::where('email', $request->email)->first();
        if ($currentUser) {
           throw new Exception('This email already exists in our system');
        }

        $deletedUser = User::onlyTrashed()->where('email', $request->email)->first();
        if ($deletedUser) {
           $deletedUser->activation_token = Str::random(60);
           $deletedUser->password = bcrypt($request->password);
           $deletedUser->save();
           $deletedUser->notify(new ApiEmailVerifyAccount(true));
           return response()->json([
              'message' => 'The account linked to this email was deleted. Check your email for recovery link',
              'type' => 'warning'
           ], 201);
        }

        $user = new User([
            'active' => true,
            'name' => $request->name ?? null,
            'login' => $request->login ?? null,
            'phone' => $request->phone ?? null,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'activation_token' => Str::random(60),
            'role_id' => $request->role_id ?? 2
        ]);
        $user->save();

        $path = public_path().'/media/users/' . $user->id;
        File::makeDirectory($path, $mode = 0777, true, true);

        $this->sendVerifyEmail($user->email);

        return response()->json([
            'message' => 'An account has been created successfully!'
        ], 201);
    }

    public function sendVerifyEmail (string $email)
    {
       try {
          $user = User::withTrashed()->where('email', $email)->firstOrFail();
       } catch (Exception $exception) {
          throw new Exception('We can\'t find this email in our system');
       }
       if (!$user->activation_token) {
          return response()->json([
             'message' => 'This email is already confirmed'
          ]);
       }
       $user->notify(new ApiEmailVerifyAccount());
       return response()->json([
          'message' => 'Email with confirmation link has been send'
       ]);
    }
}
