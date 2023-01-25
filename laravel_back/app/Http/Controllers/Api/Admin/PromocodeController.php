<?php

namespace App\Http\Controllers\Api\Admin;

use App\Promocode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PromocodeController extends AdminController
{
    public function __construct ()
    {
        $this->model = Promocode::class;
    }

    public function show($uniqueField, Request $request)
    {
        $user = Auth::user();
        $code = $this->model::where('code', strtolower($uniqueField))->firstOrFail();

        if ($code->start_at > now() || $code->end_at < now()) {
            return response()->json('Promocode is out of date',423);
        }
        $userCode = $user->promoCodes()->find($code->id);
        if ($code->usage_count >= $code->usage_limit || !is_null($userCode)) {
            return response()->json('Promocode usage limit has been reached', 423);
        }
        return response()->json($code);
    }
}
