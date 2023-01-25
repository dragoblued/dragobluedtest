<?php

namespace App\Http\Controllers\Api;

use App\CmnCourse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CmnCourseController extends Controller
{
    protected $model = CmnCourse::class;

    public function index(): JsonResponse
    {
        return response()->json(
            $this->model::get()
        );
    }

    public function show($uniqueField): JsonResponse
    {
        return response()->json(
            $this->model::where('id', $uniqueField)
                ->orWhere('route', $uniqueField)
                ->orWhere('name', $uniqueField)
                ->with(['course', 'event'])
                ->firstOrFail()
        );
    }
}
