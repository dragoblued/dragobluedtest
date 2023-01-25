<?php

namespace App\Http\Controllers\Admin;

use App\Room;
use App\Stream;
use App\User;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

use App\Setting;
use App\Facades\Uploader;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StreamController extends AdminController
{

    protected $model;

    public function __construct ()
    {
        parent::__construct();
    }

    public function init ()
    {
        $this->setPage([
            'route' => 'admin.streams',
            'title' => 'Streams - [ ADMIN ]',
            'h1'    => 'Streams'
        ]);

        $this->setModel(Stream::class);
        $this->setForm();
        $this->updateForm();
        $this->setRules();
    }

    public function index (Request $request): Renderable
    {
        $this->init();

        $items = $this->model::paginate(20);

        $data = [
            'page'  => $this->getPage(),
            'items' => $items
        ];

        return view('admin._list', $data);
    }

    public function show(int $id, Request $request)
    {
        $this->init();

        $item = $this->model::findOrFail($id);
        $room = Room::where([
            ['subject_id', $item->id],
            ['subject_type', 'App\\Stream']
        ])->firstOrCreate([
            'subject_id' => $item->id,
            'subject_type' => 'App\\Stream'
        ]);
        $item = $this->model::with(['room'])->findOrFail($id);
        $this->setH1(": {$item->title}");

        if ($request->ajax()) {
            return response()->json($item);
        }

        $data = [
            'page'  => $this->getPage(),
            'item' => $item,
            'room' => $room,
            'chatMessages' => $room->messages,
            'chatSelectedMessages' => $room->selectedMessages
        ];

        return view('admin.streams.stream', $data);
    }

    private function updateForm (): void
    {

    }

    public function create()
    {
        $this->init();
        $this->setCurrent('create');
        $currency_icon = $this->getCurrencyIcon();

        $data = [
            'page' => $this->getPage(),
            'form' => $this->getForm(),
            'currency_icon' => $currency_icon,
            'ext_links' => [
                '<link rel="stylesheet" href="'.config('app.url').'/css/lib/bootstrap-datetimepicker.min.css">'
            ],
            'ext_scripts' => [
                '<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>',
                '<script src="'.config('app.url').'/js/inc/video-hls.js"></script>',
                '<script src="'.config('app.url').'/js/libs/bootstrap-datetimepicker.min.js"></script>'
            ]
        ];
        return view('admin._form', $data);
    }

    private function syncUsersAndRooms($allowedUserIds, $roomId)
    {
        $room = Room::findOrFail($roomId);
        $room->users()->sync([]);
        foreach ($allowedUserIds as $id) {
            $user = User::find($id);
            if ($user) {
                $room->users()->sync($id, false);
            }
        }
    }


    public function store(Request $request)
    {
        $this->init();
        $request->merge([
            'name' => Str::slug($request->get('name')),
            'allowed_users' => json_decode($request->get('allowed_users')),
            'key' => Str::random()
        ]);
        $request->validate($this->rules);
//        $request = $this->preUpdating($request);

        $item = Uploader::create($this->model);
        $room = Room::create([
            'subject_id' => $item->id,
            'subject_type' => 'App\\Stream',
            'creator_id' => Auth::user()->id
        ]);
        $this->syncUsersAndRooms($item->allowed_users ?? User::pluck('id')->toArray(), $room->id);

        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Created: <b>{$item->name}</b>");
    }


    public function preUpdating($request)
    {
        $request->merge([
            'is_model_visible' => !is_null($request->get('is_model_visible')),
            'address_coordinates' => json_decode($request->get('address_coordinates')),
        ]);
        return $request;
    }

    public function edit(int $id): Renderable
    {

        $this->init();
        $this->setCurrent('edit');
        $currency_icon = $this->getCurrencyIcon();
        $item = $this->model::findOrFail($id);
        $this->setForm('poster_url.items', $this->getFiles($item, 'poster_url', ['min', '']));
        $data = [
            'page' => $this->getPage(),
            'form' => $this->getForm(),
            'item' => $item,
            'currency_icon' => $currency_icon,
            'ext_links' => [
                '<link rel="stylesheet" href="'.config('app.url').'/css/lib/bootstrap-datetimepicker.min.css">'
            ],
            'ext_scripts' => [
                '<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>',
                '<script src="'.config('app.url').'/js/inc/video-hls.js"></script>',
                '<script src="'.config('app.url').'/js/libs/bootstrap-datetimepicker.min.js"></script>'
            ]
        ];

        return view('admin._form', $data);
    }

    public function update(int $id, Request $request): RedirectResponse
    {
        $this->init();
        $this->setRule('name.required', "nullable");
        $request->merge([
            'allowed_users' => json_decode($request->get('allowed_users'))
        ]);
        $request->request->remove('name');
        $request->validate($this->rules);
//        $request = $this->preUpdating($request);

        $item = $this->model::findOrFail($id);
        $prevAllowedUsers = array_diff($item->allowed_users ?? User::pluck('id')->toArray(), $item->banned_users ?? []);

        $item = Uploader::update($item);
        $room = Room::firstOrCreate([
            'subject_id' => $item->id,
            'subject_type' => 'App\\Stream'
        ]);

        $allowedUsers = array_diff($item->allowed_users ?? User::pluck('id')->toArray(), $item->banned_users ?? []);
        if ($allowedUsers != $prevAllowedUsers) {
            $this->syncUsersAndRooms($allowedUsers, $room->id);
        }

        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Updated: <b>{$item->name}</b>");
    }

    public function fastUpdate(int $id, Request $request)
    {
        $item = Stream::findOrFail($id);
        if (!is_null($item->start_at)) {
            $request->request->remove('start_at');
        }
        $item->fill($request->all());
        $item->save();

        if ($request->ajax()) {
            $response = "Updated: <b>{$item->title}</b>";
            return response()->json($response);
        }

        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Updated: <b>{$item->title}</b>");
    }

    public function destroy(int $id, Request $request)
    {
        $this->init();
        return $this->delete($id, $request);
    }

    private function getCurrencyIcon(){
        $currency_icon = Setting::where('key','currency')->first();
        $currencies = json_decode($currency_icon->value);
        foreach ($currencies as $key => $value) {
            if ($value->selected === true) {
                $currency_icon = $value->sign;
            }
        }
        return $currency_icon;
    }

    protected function checkDrop ($item): bool {
        return true;
    }
}
