<?php

namespace App\Http\Controllers\Api;

use App\Course;
use App\Jobs\ArchiveVideo;
use App\Lesson;
use App\Topic;
use App\Traits\StatsCounter;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    use StatsCounter;
    protected $model = Course::class;
    protected $lessons;

    public function __construct () {}

    public function index(): JsonResponse
    {
        $items = $this->model::where('status', '!=', 'editing')->with(['topics'])->orderBy('order')->get();
        foreach ($items as $item) {
            if (sizeof($item->topics) > 0) {
                foreach ($item->topics as $topic) {
                    if (sizeof($topic->lessons) > 0) {
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

    public function getAllNavs(): JsonResponse
    {
        return response()->json(
            $this->model::select('title', 'route')
                ->get()
        );
    }

    public function show($uniqueField): JsonResponse
    {
        $item = $this->model::where('id', $uniqueField)
            ->orWhere('route', $uniqueField)
            ->orWhere('name', $uniqueField)
            ->with(['topics'])
            ->firstOrFail();

        $progress = (object) [];
        if (!is_null(auth('api')->user())) {
            $progress = auth('api')->user()->progress();
        }
        $isCoursePurchased = false;
        if (isset($progress->courses)) {
            foreach ($progress->courses as $courseProgress) {
                if ($courseProgress['course_id'] === $item->id && $courseProgress['is_purchased'] === 1) {
                    $isCoursePurchased = true;
                    break;
                }
            }
        }
        if ($isCoursePurchased === false && sizeof($item->topics) > 0) {
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
        return response()->json($item);
    }

    public function visitCourse(int $id)
    {
        $this->model::findOrFail($id);
        /* Увеличиваем счетчик просмотров страницы курса*/
        $this->incrementCount(
            'Course',
            $id,
            ['id'],
            'view_count'
        );
        return response()->json(true);
    }

    public function visitTopic(int $id)
    {
        Topic::findOrFail($id);
        /* Увеличиваем счетчик просмотров страницы топика*/
        $this->incrementCount(
            'Topic',
            $id,
            ['id'],
            'view_count'
        );
        return response()->json(true);
    }

    public function archiveCourseMaterials(int $id): JsonResponse
    {
        $pattern = '/\d{1,}p/';

        $course = Course::find($id)
            ->load('lessons');
        $lessons = $course
            ->lessons
            ->where('converted', true)
            ->where('video_available_formats', '!==', null)
            ->where('video_url', '!==', null)
            ->load('topic', 'course')
        ;

//        $lessons = Lesson::where('course_id', $courseId)
//            ->where('converted', true)
//            ->whereNotNull('video_available_formats')
//            ->whereNotNull('video_url')
//            ->with(['course' => function($query) {
//                $query->select('id', 'route', 'name');
//            }])
//            ->get(['id', 'route', 'course_id', 'video_url', 'video_available_formats']);
//        return response()->json(
//            $lessons
//        );
//        dd($lessons);

    //    return response()->json($lessons[0]->best_format = implode(',', $lessons[0]->video_available_formats));
        if($lessons->count() > 0) {
            $files = collect([]);

            $lessons->each(function($format) use ($pattern, $files) {
                $format->best_format = implode(array_slice($format->video_available_formats, -1));

                preg_match($pattern, $format->best_format, $matches);
                $format->pattern = $pattern;
                $format->matches = $matches;

                $files->push($format->best_format_link = substr($format->video_url, 0,-4).'_'.$matches[0].'.mp4');
            });
            return response()->json(
                $lessons
            );

            ArchiveVideo::dispatch($lessons, $files);

            return response()->json(
                'archive converting'
            );
        }
        return response()->json(
            'nothing converting'
        );
    }
}
