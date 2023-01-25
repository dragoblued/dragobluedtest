<?php


namespace App\Http\Controllers\Api;

use App\Message;
use App\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController
{
    protected $model = Message::class;

    public function index(Request $request): JsonResponse
    {
        $result = [];
        $limit = $request->get('limit') ? $request->get('limit') : 10;
        $page = $request->get('page') ? $request->get('page') : 0;
        $direction = $request->get('direction') ?? 'asc';
        $result['limit'] = $limit;
        $result['page'] = $page;
        if ($request->get('lesson_id')) {
            $room = Room::where('lesson_id', $request->get('lesson_id'))
                ->firstOrCreate([
                    'lesson_id' => $request->get('lesson_id')
                ]);
            $items = $this->model::where([
                ['room_id', $room->id],
                ['deleted', '!=', 1],
                ['link', null]
            ])
                ->with(['user','attached'])
                ->orderBy('id', 'desc')
                ->get()
                ->slice($limit * $page, $limit)
                ->sortBy(['id', $direction])
                ->values();
            $result['room_id'] = $room->id;
        } else {
            $items = $this->model::where([
                ['deleted', '!=', 1],
                ['link', null]
            ])
                ->with(['user','attached'])
                ->orderBy('id', 'desc')
                ->get()
                ->slice($limit * $page, $limit)
                ->sortBy(['id', $direction])
                ->values();
        }
        $result['messages'] = $items;

        return response()->json(
            (object) $result
        );
    }
}
