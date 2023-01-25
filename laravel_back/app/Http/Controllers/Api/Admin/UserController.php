<?php

namespace App\Http\Controllers\Api\Admin;

use App\Certificate;
use App\Lesson;
use App\Notification;
use App\TestResult;
use App\Ticket;
use App\Traits\StatsCounter;
use App\UserCourse;
use App\UserLesson;
use App\UserTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\User;
use App\Role;
use App\Group;
use Illuminate\Support\Facades\Log;

class UserController extends AdminController
{
    use StatsCounter;

	public function __construct ()
	{
		$this->model = User::class;
		$this->rules = [
			'active'   => 'required',
			'login'    => "nullable|alpha_dash|between:3,191|unique:users,login",
			'email'    => "required|email|max:191|unique:users,email",
			'password' => "required|min:6|confirmed",
			'role' 	  => "required"
		];
	}

	public function index (Request $request)
	{
		$items = $this->model::orderBy('id', 'desc')
		->with(['role', 'groups'])
		->get();

		return $items;
	}

	public function show ($uniqueField, Request $request)
	{
        return Auth::user();
	}

	public function store (Request $request)
	{
		$request->validate($this->rules);
		$request->request->add(['password' => Hash::make($request->password)]);

		$item = $this->model::create($request->all());
		$item->groups()->attach($request->group);

		$item = $this->model::with(['role', 'groups'])->find($item->id);

		return $item;
	}

	protected function preUpdate (int $id, Request $request) {
		$this->setRule('active.required', "nullable");
		$this->setRule('login.unique', "users,login,{$id}");
		$this->setRule('login.required', "nullable");
		$this->setRule('email.unique', "users,email,{$id}");
		$this->setRule('email.required', "nullable");
		$this->setRule('password.required', "nullable");
		$this->setRule('role.required', "nullable");
	}

	public function update (int $id, Request $request)
	{
		$item = Auth::user();
		$check = $this->checkUpdate($item);
		if($check) {
			$this->preUpdate($item->id, $request);

			$request->validate($this->rules);

			if(!is_null($request->password)) {
				$request->request->add(['password' => Hash::make($request->password)]);
			} else {
				$request->request->remove('password');
			}

			Log::debug($request->all());

			$item->fill($request->all());
			$item->save();

         if (isset($request->group)) {
             $item->groups()->sync($request->group);
         }

			$item = $this->model::with(['role', 'groups'])->find($item->id);

			return $item;
		}

		return response()->json(['message' => 'access denied'], 403);
	}

    public function getProgress(): JsonResponse {
	    $user = Auth::user();
	    if (!is_null($user)) {
            $id = $user->id;
            return response()->json($user->progress());
        }
	    return response()->json([]);
    }

    public function storeProgress(Request $request, $uniqueField)
    {
        $user = Auth::user();
        foreach ($request->all() as $item) {
            if ($item['currentTiming'] > 5 || $item['isViewed']) {
                $body = [$item['id'] => [
                    'current_timing' => $item['currentTiming'],
                    'current_timing_percent' => $item['currentTimingPercent'],
                    'is_viewed' => $item['isViewed']
                ]];

                if ($user->lessons->contains($item['id'])) {

                    /* Проверка нужно ли увеличивать счетчик просмотров*/
                    $prevIsViewed = $user->lessons->find($item['id'])->pivot->is_viewed;
                    if ($item['isViewed'] == true && $prevIsViewed != true) {
                        $this->incrementLessonViewCount($item['id'], $user);
                    }

                    $user->lessons()->sync($body, false);
                } else {
                    $user->lessons()->attach($body);
                    /* Проверка нужно ли увеличивать счетчик просмотров*/
                    if ($item['isViewed'] == true) {
                        $this->incrementLessonViewCount($item['id'], $user);
                    }
                }
            }
        }
        return null;
    }

    public function incrementViewCountForUser(Request $request, $userId, $lessonId){
        $user_lesson = UserLesson::where('lesson_id', $lessonId)
                                  ->where('user_id', $userId)
                                  ->first();

        if ($user_lesson != null) {
            // Устанавливаем время просмотра
            $timeView = json_decode($user_lesson->times_view);
            $timeView[] = Carbon::now()->toDateTimeString();
            $user_lesson->times_view = json_encode($timeView);
            // Сохраняем ip адрес пользователя
            $userIpAdress = json_decode($user_lesson['user_ip_address']);
            $userIpAdress[] = $request->get('ip');
            $user_lesson['user_ip_address'] = json_encode($userIpAdress);
            // увеличываем количество просмотра для отдельного юзера
            $lessonViewCount = $user_lesson->view_count;
            $user_lesson->view_count = $lessonViewCount + 1;
            $user_lesson->save();
        }else{
            $userLesson = new UserLesson();
            $userLesson['user_id'] = $userId;
            $userLesson['lesson_id'] = $lessonId;
            $timeView[] = Carbon::now()->toDateTimeString();
            $userLesson['times_view'] = json_encode($timeView);
            $userLesson['is_viewed'] = true;
            if ($request->get('ip')) {
                $userIpAdress[] = $request->get('ip');
                $userLesson['user_ip_address'] = json_encode($userIpAdress);
            }
            $userLesson->save();
        }
        return response()->json([
            'message' =>  'Updated!'
        ]);
    }

    private function incrementLessonViewCount(int $id, User $user)
    {
        /* Увеличиваем счетчик в модели Lesson для статистики */
        $this->incrementCount(
            'Lesson',
            $id,
            ['id'],
            'view_count'
        );

        /* Также нужно увеличить счетчик прогресса в pivot таблицах UserTopic и UserCourse */
        $lesson = Lesson::findOrFail($id);
        $topic = $lesson->topic;
        $course = $topic->course;

        if (!$user->topics->contains($topic->id)) {
            $user->topics()->attach([$topic->id => [
                'lessons_view_count' => 1
            ]]);
        } else {
            $count1 = (int) $user->topics()->where('topic_id', $topic->id)->first()->pivot->lessons_view_count;
            $user->topics()->sync([$topic->id => [
                'lessons_view_count' => $count1 + 1
            ]], false);
        }

        if (!$user->courses->contains($course->id)) {
            $user->courses()->attach([$course->id => [
                'lessons_view_count' => 1
            ]]);
        } else {
            $count2 = (int) $user->courses()->where('course_id', $course->id)->first()->pivot->lessons_view_count;
            $user->courses()->sync([$course->id => [
                'lessons_view_count' => $count2 + 1
            ]], false);
        }
    }

    public function hasPermissions(Request $request)
    {
	    $user = Auth::user();
	    return response()->json(
	        ['allowed' => $user->hasPermissions(json_decode($request->get('permissions')))]
        );
    }

	protected function checkUpdate ($item): bool {
		return $item->login === 'ROOT' ? false : true;
	}

	protected function checkDrop ($item): bool {
		return $item->login === 'ROOT' ? false : true;
	}
}
