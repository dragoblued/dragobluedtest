<?php

namespace App\Http\Controllers\Api;

use App\Jobs\SendCmnEmail;
use App\Feedback;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeedbackController extends Controller
{
    private $model = Feedback::class;
    private $rules = [
        'name'   => 'required',
        'email'  => 'required|email',
        'text'   => 'required',
    ];

    public function send (Request $request): Feedback
    {
        $item = $this->store($request);
        $this->sendEmail($item);

        return $item;
    }

    public function store (Request $request)
    {
        $request->validate($this->rules);
        $item = $this->model::create($request->all());

        return $item;
    }

    private function sendEmail(Feedback $item)
    {
        $users = User::get();
        foreach ($users as $user) {
            if ($user->hasGroups('FEEDBACK_NOTIFIES')) {
                SendCmnEmail::dispatch($user->email, 'Feedback', 'email.feedback', $item);
            }
        }

    }
}
