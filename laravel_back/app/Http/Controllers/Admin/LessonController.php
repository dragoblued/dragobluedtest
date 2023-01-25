<?php

namespace App\Http\Controllers\Admin;

use App\Classes\RemoveFiles;
use App\Jobs\CleanVideoDir;
use App\Jobs\VideoMainMp4ToHls;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use App\Classes\UpdateTotalCount;
use App\Facades\Uploader;
use File;
use App\Lesson;
use App\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class LessonController extends AdminController
{
   protected $model;

   public function __construct ()
   {
      parent::__construct();
   }

   public function init ()
   {
      $this->setPage([
         'route' => 'admin.lessons',
         'title' => 'Lessons - [ ADMIN ]',
         'h1'    => 'Lessons'
      ]);

      $this->setModel(Lesson::class);
      $this->setForm();
      $this->updateForm();
      $this->setRules();
   }

   private function updateForm (): void
   {
      $topics = Course::orderBy('id')
         ->with('topics')
         ->get(['id', 'name'])
         ->mapWithKeys(function ($item) {
            return [
               $item['name'] => $item
                  ->topics
                  ->mapWithKeys(function($item) {
                     return [
                        $item->id => $item->name
                     ];
                  })
            ];
         });
      $this->setForm('topic_id.items', $topics);
   }

   public function index (Request $request): Renderable
   {
      $this->init();

      $items = $this->model::orderBy('topic_id')->orderBy('order')->get();

//        if (request()->has('course')) {
//            $items->orderBy('course_title');
//        } elseif(request()->has('topic')) {
//            $items->orderBy('topic_title');
//        }
//        else {
//            $items->orderBy('topic_title');
//        }

//        $items = $this->model::orderBy('topic_id')
//            ->orderBy('order')
//            ->with('topic')
//            ->get();

      $data = [
         'page'  => $this->getPage(),
         'items' => $items,
         'datatableData'  => (object) [
            'isColumnFilters' => true,
            'noSortColumns' => [8,9,10],
            'noFilterColumns' => [5,8,9,10]
         ]
      ];

      return view('admin._list', $data);
   }

   public function create (): Renderable
   {
      $this->init();
      $this->setCurrent('create');
      $this->setForm('order.item', 100);

      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
      ];
      return view('admin._form', $data);
   }

   public function store (Request $request): RedirectResponse
   {
      $this->init();

      if(!$request->filled('route')) {
         $request->merge([
            'route' => Str::slug($request->title),
         ]);
      }
      $request->merge([
         'name' => Str::slug($request->title)
      ]);

      $request->validate($this->rules);
      if (!is_null(Auth::user())) {
         $request->request->add(['user_creator_id' => Auth::user()->id]);
      }

      $item = Uploader::create($this->model);

      (new UpdateTotalCount())->updateTotalLessons();

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Added. ID: <b>{$item->title}</b>");
   }

   public function edit (int $id): Renderable
   {
      $this->init();
      $this->setCurrent('edit');

      $item = $this->model::findOrFail($id);
      $this->setForm('poster_url.items', $this->getFiles($item, 'poster_url'));
      $this->setForm('video_url.items', $this->getFiles(
         $item,
         'promo_video_url',
         $item->promo_video_available_formats ? [$item->promo_video_available_formats[0]] : []
      ));
      $this->setForm('video_url.disabled', ($item->converted === 3));
      $this->setForm('order.item', $item->order);

      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
         'item' => $item,
      ];

      return view('admin._form', $data);
   }

   public function update ($id, Request $request): RedirectResponse
   {
      $this->init();
//        $this->setRule('route.unique', "lessons,route,{$id}");
//        $this->setRule('name.unique', "lessons,name,{$id}");
//        $request->validate($this->rules);

      $item = $this->model::findOrFail($id);
      if (!is_null(Auth::user())) {
         $item->user_creator_id = Auth::user()->id;
      }
      Uploader::update($item);

      (new UpdateTotalCount())->updateTotalLessons();
//        $item->fill($request->all());
//        $item->save();
//        $item->lessons()->sync($item->lesson);

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Updated: <b>{$item->title}</b>");
   }

   public function destroy (int $id, Request $request)
   {
      $this->init();
//        $videoLessons = File::allFiles('video/lessons/');
//        $destroyLesson = preg_grep("/n{$id}_/", $videoLessons);
//        File::delete($destroyLesson);
      $lesson = Lesson::findOrFail($id);

      $removeFiles = new RemoveFiles($lesson);
      $removeFiles->RemoveDirectory();

      (new UpdateTotalCount)->updateTotalLessons();
      (new UpdateTotalCount)->updateTotalTopics();
      (new UpdateTotalCount)->updateTotalDuration($id, true);

      return $this->delete($id, $request);
   }

   protected function checkDrop ($item): bool {
      return true;
   }

   public function reconvertMp4ToHls (int $id) {
      $item = Lesson::findOrFail($id);

      /* change status to 3 - means convertation in process */
      $data = ['converted' => 3];
      $user = Auth::user();
      if (!is_null($user)) {
         $data['user_creator_id'] = $user->id;
      }
      $item->update($data);

      Queue::connection("longFilesJob")->push((new CleanVideoDir(
         public_path('media/hls/').$item->name
      )));
      Queue::connection("longFilesJob")->push((new VideoMainMp4ToHls(
         $item,
         'video_available_formats',
         'name',
         'lessons',
         'hls'
      )));

      $response = "Reconvertation started: <b>{$item->title}</b>";
      return response()->json($response);
   }

   public function typesToHls (int $moduleId)
   {
      $module = Course::findOrFail($moduleId);
      foreach ($module->topics as $topic) {
         foreach ($topic->lessons as $lesson) {
            $lesson->video_type = 'm3u8';
            $lesson->save();
         }
      }
      return response()->json('done');
   }
}
