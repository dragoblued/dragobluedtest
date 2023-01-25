<?php

namespace App\Http\Controllers\Api\Admin;

use App\Course;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    protected $model = Course::class;

    public function __construct () {}

    public function index(): JsonResponse
    {
        $items = $this->model::where('status', '!=', 'editing')->with(['topics'])->orderBy('order')->get();
        if (is_null(Auth::guard('api')->user())) {
            return response()->json(['error' => 'Authenticated user has not been found'], 401);
        }
        $progress = Auth::guard('api')->user()->progress();
        foreach ($items as $item) {
            if (isset($progress->courses)) {
                foreach ($progress->courses as $courseProgress) {
                    if ($courseProgress['course_id'] === $item->id && $courseProgress['is_purchased'] === 1) {
                        continue 2;
                    }
                }
            }
            if (sizeof($item->topics) > 0) {
                foreach ($item->topics as $topic) {
                    if (sizeof($topic->lessons) > 0) {
                        if (isset($progress->topics)) {
                            foreach ($progress->topics as $topicProgress) {
                                if ($topicProgress['topic_id'] === $topic->id && $topicProgress['is_purchased'] === 1) {
                                    continue 2;
                                }
                            }
                        }
                        foreach ($topic->lessons as $lesson) {
                            if ($lesson->is_free !== 1) {
                                $lesson->setHidden(['video_url', 'video_original_name', 'video_available_formats']);
                            }
                        }
                    }
                }
            }
        }
        return response()->json($items);
    }
}
