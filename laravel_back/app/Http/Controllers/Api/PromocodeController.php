<?php

namespace App\Http\Controllers\Api;

use App\Promocode;
use App\Traits\StatsCounter;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class PromocodeController extends Controller
{
    protected $model = Promocode::class;

    public function show(string $code): JsonResponse
    {
        $code = $this->model::where('code', $code)->firstOrFail();
        if ($code->start_at > now() || $code->end_at < now()) {
            return response()->json('Promocode is out of date',423);
        }
        if ($code->usage_count >= $code->usage_limit) {
            return response()->json('Promocode usage limit has been reached',423);
        }
        return response()->json($code);
    }
}
