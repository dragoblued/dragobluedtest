<?php

namespace App\Http\Controllers\Api;

use App\Lesson;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class LessonController extends Controller
{
    protected $model = Lesson::class;

    public function index(): JsonResponse
    {
        return response()->json(
            $this->model::orderBy('order')->get()
        );
    }

    public function show($uniqueField): JsonResponse
    {
        return response()->json(
            $this->model::where('id', $uniqueField)
                ->orWhere('route', $uniqueField)
                ->orWhere('name', $uniqueField)
                ->firstOrFail()
        );
    }
}
