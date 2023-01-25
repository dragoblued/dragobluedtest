<?php

namespace App\Http\Controllers\Api;

use App\Event;
use App\Traits\StatsCounter;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    use StatsCounter;
    protected $model = Event::class;

    public function index(): JsonResponse
    {
        return response()->json(
            array_map(function ($item) {
                $groups = array();
                foreach ($item['dates'] as $date) {
                    if ($date['is_expired'] != 1) {
                        if ($date['start'] > Carbon::now()->format('Y-m-d')) {
                            $groups[$date['year']][] = $date;
                        }
                    }
                }
                $item['dates'] = (object) $groups;
                return $item;
            }, $this->model::where('status', '=', 'published')->with(['dates', 'gallery'])->get()->toArray())
        );
    }

    public function visitCourse(int $id)
    {
        $this->model::findOrFail($id);
        /* Увеличиваем счетчик просмотров страницы курса*/
        $this->incrementCount(
            'Event',
            $id,
            ['id'],
            'view_count'
        );
        return response()->json(true);
    }
}
