<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Uploader;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Support\Renderable;

use App\User;
use App\Role;
use App\Group;

class UserController extends AdminController
{
    protected $model;

    public function __construct ()
    {
        parent::__construct();
    }

    public function init () {
        $this->setPage([
            'route' => 'admin.users',
            'title' => 'Users - [ ADMIN ]',
            'h1'    => 'Users',
            'func' => [
                'actions'
            ]
        ]);
        $this->setModel(User::class);
        $this->setForm();
        $this->updateForm();
        $this->setRules();
    }

    private function updateForm (): void
    {
        $roles = Role::orderBy('id')->pluck('name', 'id')->toArray();

        $groups = Group::orderBy('id')->get()->mapWithKeys(function ($group) {
            return [$group->id => "<span title=\"{$group->description}\">{$group->name}</span>"];
        })->toArray();

        $this->setForm('role_id.items', $roles);
        $this->setForm('groups.items', $groups);
    }

    public function index (Request $request): Renderable
    {
        $this->init();

        $items = $this->model::where('id', '!=', 1)
            ->with('groups')
            ->get();

        $data = [
            'page'  => $this->getPage(),
            'items' => $items,
            'datatableData'  => (object) [
                'isColumnFilters' => true,
                'noSortColumns' => [1,8,10],
                'noFilterColumns' => [0,1,8,9,10]
            ]
        ];
        return view('admin._list', $data);
    }

    public function show($uniqueField, Request $request)
    {
        $this->init();
        return $this->model::with(['role'])->findOrFail($uniqueField);
    }

    public function create (): Renderable
    {
        $this->init();
        $this->setCurrent('create');

        $data = [
            'page' => $this->getPage(),
            'form' => $this->getForm(),
        ];
        return view('admin._form', $data);
    }

    public function store (Request $request): RedirectResponse
    {
        $this->init();
        $request->validate($this->rules);
        $request->merge(['password' => Hash::make($request->get('password'))]);

        $item = Uploader::create($this->model);
        if (!isset($request->groups)) {
            $request->request->add(['groups' => []]);
        }
        $item->groups()->attach($request->groups);

        $path = public_path().'/media/users/' . $item->id;
        File::makeDirectory($path, $mode = 0777, true, true);

        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Added: <b>{$item->email}</b>");
    }

    public function edit (int $id): Renderable
    {
        $this->init();
        $this->setCurrent('edit');

        $item = $this->model::with(['groups'])->findOrFail($id);
        $this->setForm('avatar_url.items', $this->getFiles($item, 'avatar_url'));
        $item = $item->toArray();
        $item['groups'] = array_map(function ($group) {
            return $group['id'];
        }, $item['groups']);

        $data = [
            'page' => $this->getPage(),
            'form' => $this->getForm(),
            'item' => $item,
        ];
        return view('admin._form', $data);
    }

    public function update (int $id, Request $request)
    {
        $this->init();
        $this->setRule('login.unique', "users,login,{$id}");
        $this->setRule('login.required', "nullable");
        $this->setRule('email.unique', "users,email,{$id}");
        $this->setRule('email.required', "nullable");
        $this->setRule('password.required', "nullable");
        $request->validate($this->rules);

        if(!is_null($request->password)) {
            $request->request->add(['password' => Hash::make($request->password)]);
        } else {
            $request->request->remove('password');
        }

        if (!isset($request->groups) && !$request->ajax()) {
            $request->request->add(['groups' => []]);
        }

        $item = $this->model::findOrFail($id);

        if($this->checkUpdate($item)) {

            Uploader::update($item);
            $item->groups()->sync($request->groups);

            if ($request->ajax()) {
                $response = 'User status with email: <b>'.$item->email.'</b> has been changed';
                return response()->json($response);
            }

            return redirect()
                ->route("{$this->page['route']}.index")
                ->with('alert', "Updated: <b>{$item->email}</b>");
        }

        return $this->errorDrop($request);
    }

    public function destroy (int $id, Request $request)
    {
        $this->init();
        $item = $this->model::findOrFail($id);
        if($this->checkDrop($item)) {
//            File::deleteDirectory(public_path().'/media/users/' . $id);
            // !SOFT DELETE!
            $item->delete();
            if($request->ajax()) {
                $response = "Removed: <b>{$item->email}</b>";
                return response()->json($response);
            }

            return redirect()
                ->route("{$this->page['route']}.index")
                ->with('alert', "Removed: <b>{$item->email}</b>");
        }

        return $this->errorDrop($request);
    }

    public function checkUpdate ($item): bool {
        return $item->id !== 1;
    }

    public function checkDrop ($item): bool {
        return $item->id !== 1;
    }

    public function errorDrop (Request $request)
    {
        if($request->ajax()) {
            $error = 'Removing or updating ROOT is forbidden';
            return response()->json($error, 423);
        }
        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Removing or updating <b>ROOT</b> is forbidden");
    }

    public function removeDevice(int $id, int $deviceIdx): JsonResponse
    {
        $user = User::findOrFail($id);
        $devices = $user->device_ids ?? [];
        if (array_key_exists($deviceIdx, $devices)) {
            unset($devices[$deviceIdx]);
            $user->device_ids = array_values($devices);
            $user->save();
            return response()->json('ok');
        } else {
            return response()->json('Device has not been found', 404);
        }
    }

    public function removeAllDevices(): JsonResponse
    {
        $users = User::get();
        foreach ($users as $user){
            $user->device_ids = null;
            $user->save();
        }
        return response()->json('ok');
    }

    public function showUserList(): JsonResponse
    {
        return response()->json([
            'users' => User::select('id', 'name', 'email')->where('id', '!=', 1)->get(),
            'groups' => []
        ]);
    }

    public function showUserInfo(int $id, string $simplified): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($simplified === 'true') {
            return response()->json([
                'user' => $user
            ]);
        }

        $progress = (array) $user->progress();
        $courses = $user->courses;
        $topics = [];
        $lessons = [];
        foreach ($courses as $course) {
            $topics[$course->id] = $course->topicsSimple;
            $lessons[$course->id] = $course->lessons->groupBy('topic_id');
            $courseProgress = $progress['courses']->first(function($item) use ($course) {
                return $item->course_id === $course->id;
            });
            if ($courseProgress) {
                $course['is_purchased'] = $courseProgress['is_purchased'];
                $course['lessons_view_count'] = $courseProgress['lessons_view_count'];
                $course['invoice_id'] = $courseProgress['invoice_id'];
            }
            foreach ($topics[$course->id] as $topic) {
                $topicProgress = $progress['topics']->first(function($item) use ($topic) {
                    return $item->topic_id === $topic->id;
                });
                if ($topicProgress) {
                    $topic['is_purchased'] = $topicProgress['is_purchased'];
                    $topic['lessons_view_count'] = $topicProgress['lessons_view_count'];
                    $topic['invoice_id'] = $topicProgress['invoice_id'];
                }
                foreach ($lessons[$course->id][$topic->id] as $lesson) {
                    $lessonProgress = $progress['lessons']->first(function($item) use ($lesson) {
                        return $item->lesson_id === $lesson->id;
                    });
                    if ($lessonProgress) {
                        $lesson['is_purchased'] = $lessonProgress['is_purchased'];
                        $lesson['is_viewed'] = $lessonProgress['is_viewed'];
                    }
                }
            }
        }

        return response()->json([
            'user' => $user,
            'courses' => $courses,
            'topics' => $topics,
            'lessons' => $lessons,
            'progress' => $progress
        ]);
    }
}
