<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Password;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function show(Request $request)
    {
        if ($request->user() !== null) {
            return $request->user()->hasVerifiedEmail()
                ? redirect($this->redirectPath())
                : view('auth.verify');
        } else {
            return view('auth.verify');
        }
    }

    public function resend(Request $request)
    {
        if ($request->user() !== null) {
            if ($request->user()->hasVerifiedEmail()) {
                 return redirect($this->redirectPath());
            }
        }

        $response = $this->broker()->sendResetLink(
            ['email' => session('email')]
        );

        $message = $response == Password::RESET_LINK_SENT ? 'new verification link has been sent' : 'An error has occurred, please try again';

        return back()->with('resent', $message);
    }

    public function broker()
    {
        return Password::broker();
    }
}
