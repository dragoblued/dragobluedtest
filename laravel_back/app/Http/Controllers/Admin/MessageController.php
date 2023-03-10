<?php

namespace App\Http\Controllers\Admin;

use App\Events\Message as MessageEvent;
use App\Jobs\SendCmnEmail;
use App\Message;
use App\Notification;
use App\Room;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends AdminController
{
    protected $model;

    public function __construct ()
    {
        parent::__construct();
        $this->setModel(Message::class);
        $this->rules = [
            'room_id'   => 'nullable|integer',
            'user_id'   => 'sometimes|integer',
            'link'   => 'nullable|integer',
            'text'   => 'nullable',
            'status'   => 'nullable|integer',
        ];
    }

    public function init () {}

    public function store (Request $request, $shouldBroadcastToCurrentUser = '0', $shouldNotify = '1'): JsonResponse
    {
        $request->validate($this->rules);
        $user = Auth::user();

        /*Если user не админ, то проверяем наличие room в бд(тут предполагается,
        * что админ не может создавать room, только отвечать на письма пользователей)*/
        $room = null;
        if ($request->get('room_id')) {
            $room = Room::findOrFail($request->get('room_id'));
        } elseif ($user->role_id !== 1 && $request->get('subject_id') && $request->get('subject_type')) {
            $room = Room::where([
                ['subject_id', (int) $request->get('subject_id')],
                ['subject_type', $request->get('subject_type')],
                ['creator_id', $user->id]
            ])
                ->firstOrCreate([
                    'subject_id' => $request->get('subject_id'),
                    'subject_type' => $request->get('subject_type'),
                    'creator_id' => $user->id
                ]);
        } else {
            throw new Exception('Room has not been found');
        }
        $user->rooms()->sync($room->id, false);

        $request->merge(['room_id' => $room->id]);
        $item = $this->model::create($request->all());

        if (!is_null($item->linkMessage)) {
            $user = Auth::user();
            $toUser = $item->linkMessage->user;
            if ($user->id !== $toUser->id) {
                $notification = new Notification([
                    'user_id' => $toUser->id,
                    'type' => 'chat-message',
                    'name' => 'You were mentioned in the public chat',
                    'message_id' => $item->id
                ]);
                $notification->save();
            }
        }

        $result = $this->model::with(['linkMessage'])->findOrFail($item->id);

        MessageEvent::dispatch($result->room_id, $result->id, $result, 'add', $shouldBroadcastToCurrentUser === '1');

        $result->url_from = $request->get('url_from');
        if ($shouldNotify === '1' && $item->status === 0) {
            $this->emailNotify($result);
        }

        return response()->json($result);
    }

    /* Оповещения на почту приходят всем админам, имеющих группу CHAT_NOTIFIES*/
    public function emailNotify (Message $message)
    {
        $users = User::where('role_id', 1)->get();
        foreach ($users as $user) {
            if ($user->hasGroups('CHAT_NOTIFIES') && $message->user_id !== $user->id) {
                SendCmnEmail::dispatch($user->email, 'Chat', 'email.chat_notify', $message);
            }
        }
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $user = Auth::user();
        $request->validate($this->rules);
        $item = $this->model::findOrFail($id);
        $item->fill($request->only(['text', 'status']));
        if ($request->get('status')) {
            if (!$user->isAdmin()) {
                return response()->json('You are not allowed change message status', 403);
            }
        }
        if ($request->get('text') && $user->id !== $item->user_id) {
            return response()->json(['message' => 'You are not allowed change this message'], 403);
        }
        $item->save();
        return response()->json($item);
    }

    public function destroy (int $id, Request $request, $shouldBroadcastToCurrentUser = '0'): JsonResponse
    {
        $item = $this->model::with(['attached'])->findOrFail($id);
        if($this->checkDrop($item)) {
            foreach ($item->attached()->get() as $attachedItem) {
                $attachedItem->link = null;
                $attachedItem->save();
            }
            if (!is_null($item->linkMessage)) {
                $toUser = $item->linkMessage->user;
                $notification = Notification::where([
                    ['user_id', $toUser->id],
                    ['message_id', $item->id]
                ])->first();
                if ($notification) {
                    $notification->delete();
                }
            }
            $item->delete();
            MessageEvent::dispatch($item->room_id, $item->id, $item, 'delete', $shouldBroadcastToCurrentUser === '1');
            return response()->json(null);
        }

        return response()->json(['error' => 'Deleting this item is forbidden'], 423);
    }
}
