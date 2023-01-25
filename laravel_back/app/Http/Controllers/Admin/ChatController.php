<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendCmnEmail;
use App\Message;
use App\Events\Message as MessageEvent;
use App\Notification;
use App\Room;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends AdminController
{
    protected $model;

    public function __construct ()
    {
        parent::__construct();
    }

    public function init ()
    {
        $this->setPage([
            'route' => 'admin.chat',
            'title' => 'Chat - [ ADMIN ]',
            'h1'    => 'Chat',
            'func'  => []
        ]);
        $this->rules = [
            'room_id'   => 'nullable|integer',
            'user_id'   => 'required|integer',
            'link'   => 'nullable|integer',
            'text'   => 'nullable'
        ];
        $this->setModel(Message::class);
    }

    public function index (Request $request): Renderable
    {
        $this->init();

        $users = collect();
        foreach (User::select(['id', 'active', 'avatar_url', 'email', 'name', 'surname', 'role_id'])->get() as $user) {
//            dump($user->role_id.'  '.count($user->lessonRooms));
            if ($user->role_id !== 1) {
                if (count($user->lessonRooms) > 0) {
                    foreach ($user->lessonRooms as $room) {
                        $room->calcNewMessagesCount();
                    }
                    $user->freshMessagesState();
                    $users->push($user);
                }
            }
        }
//        dd($users);
//      $users->sortByDesc('lastMessageDate');

        $data = [
            'page'  => $this->getPage(),
            'users' => $users
        ];

        return view('admin.chat', $data);
    }

    public function show (int $roomId, Request $request): JsonResponse
    {
        $this->init();
        $room = Room::with(['subject'])->findOrFail($roomId);
        foreach ($room->messages()->where('status', 0)->get() as $newMessage) {
            $newMessage->status = 1;
            $newMessage->save();
        }
        $result = ['messages' => $room
            ->messages()
            ->with(['linkMessage'])
            ->orderBy('id', 'desc')
            ->get()
            ->slice(0, 10)
            ->sortBy('id')
            ->values()];
        if ($request->get('marked')) {
            $result['markedMessages'] = $room
                ->selectedMessages()
                ->with(['linkMessage'])
                ->get()
                ->slice(0, 50)
                ->values();
        }
        $result['room'] = $room;
        $result['users'] = $room->simpledUsers;
        return response()->json($result);
    }

    public function showMarked (int $roomId): JsonResponse
    {
//        $user = Auth::user();
//        if (!$user->isAdmin()) {
//            return response()->json('You are not allowed get marked messages', 403);
//        }
        $room = Room::findOrFail($roomId);
        $items = $room
            ->selectedMessages()
            ->with(['linkMessage'])
            ->get()
            ->slice(0, 50)
            ->values();
        $result['messages'] = $items;
        $result['room'] = $room;
        $result['users'] = $room->simpledUsers;
        return response()->json($items);
    }

    public function showRooms (int $userId): JsonResponse
    {
        $this->init();
        $user = User::findOrFail($userId);
        $userRooms = $user->lessonRooms()->with(['subject'])->get();
        return response()->json($userRooms);
    }

    public function indexJSON (Request $request)
    {
        $this->init();
        $result = [];
        $items = [];
        $limit = $request->get('limit') ? (int) $request->get('limit') : 10;
        $page = $request->get('page') ? (int) $request->get('page') : 0;
        $result['limit'] = $limit;
        $result['page'] = $page;

        $room = Room::findOrFail($request->get('room_id'));
        if (!is_null($room)) {
            $items = $this->model::where([
                ['room_id', $room->id]
            ])
                ->with(['linkMessage'])
                ->orderBy('id', 'desc')
                ->get()
                ->slice($limit * $page, $limit)
                ->sortBy('id')
                ->values();
            $result['room_id'] = $room->id;
        }
        $result['messages'] = $items;

        return response()->json(
            (object) $result
        );
    }
}
