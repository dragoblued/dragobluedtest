<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    protected $model = Setting::class;

    public function index(): JsonResponse
    {
        return response()->json($this->model::get());
    }

    public function isPaymentEnabled(): JsonResponse
    {
       $response = $this->model::where('key', 'is_payment_enabled')->firstOrFail()->value;
       return response()->json($response);
    }
}
