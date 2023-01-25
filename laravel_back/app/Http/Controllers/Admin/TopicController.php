<?php

namespace App\Http\Controllers\Admin;

use App\Classes\RemoveFiles;
use App\Jobs\PosterFromFrame;
use App\Jobs\VideoChangePromoTiming;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use File;
use App\Classes\UpdateTotalCount;
use App\Course;
use App\Facades\Uploader;
use App\Topic;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;


class TopicController extends AdminController
{
   protected $model;

   public function __construct ()
   {
      parent::__construct();
   }

   public function init ()
   {
      $this->setPage([
         'route' => 'admin.topics',
         'title' => 'Topics - [ ADMIN ]',
         'h1'    => 'Topics'
      ]);

      $this->setModel(Topic::class);
      $this->setForm();
      $this->updateForm();
      $this->setRules();
   }

   private function updateForm (): void
   {
      $courses = Course::pluck('name', 'id')->toArray();

      $this->setForm('course_id.items', $courses);
   }

   public function index (Request $request): Renderable
   {
      $this->init();

      $items = $this->model::orderBy('order')
         ->orderBy('id', 'desc')
         ->paginate(20);

      $data = [
         'page'  => $this->getPage(),
         'items' => $items,
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
      $this->prepareRequest($request);
      if(!$request->filled('route')) {
         $request->merge([
            'route' => Str::slug($request->title),
         ]);
      }
      $request->merge([
         'name' => Str::slug($request->title)
      ]);
      $request->validate($this->rules);

      $item = Uploader::create($this->model);

      (new UpdateTotalCount())->updateTotalTopics();
      (new UpdateTotalCount())->updateTotalLessons();

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Added: <b>{$item->title}</b>");
   }

   public function edit (int $id): Renderable
   {
      $this->init();
      $this->setCurrent('edit');

      $item = $this->model::findOrFail($id);
      $this->setForm('poster_url.items', $this->getFiles($item, 'poster_url'));
//        $this->setForm('video_preview_img_url.items', $this->getFiles($item, 'video_preview_img_url'));
      $this->setForm('order.item', $item->order);

      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
         'item' => $item
      ];
      return view('admin._form', $data);
   }

   private function prepareRequest(Request $request)
   {
      if(!$request->filled('route')) {
         $request->merge([
            'route' => str_slug($request->name)
         ]);
      }
      $duration = $request->get('total_lessons_duration');
      if (is_array($duration)) {
         $seconds = 0;
         $mult = [1, 60, 3600];
         foreach (array_reverse($duration) as $index => $time) {
            $seconds += $time * $mult[$index];
         }
         $request->merge([
            'total_lessons_duration' => $seconds
         ]);
      }
   }

   public function update ($id, Request $request): RedirectResponse
   {
      $this->init();
      $this->setRule('route.unique', "topics,route,{$id}");
      $this->setRule('name.unique', "topics,name,{$id}");
      $this->prepareRequest($request);
      $request->validate($this->rules);

      $item = $this->model::findOrFail($id);

      Uploader::update($item);

      (new UpdateTotalCount())->updateTotalTopics();
      (new UpdateTotalCount())->updateTotalLessons();

//        $item->fill($request->all());
//        $item->save();
//        $item->lessons()->sync($item->lesson);

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Updated: <b>{$item->title}</b>");
   }

   public function reconvertPromoToSec(int $id, Request $request)
   {
      $this->init();
      $item = $this->model::findOrFail($id);
      $sec = $request->get('secondsNum');
      if ($sec) {
         Queue::connection("longFilesJob")->push((new VideoChangePromoTiming(
            $item, (int) $sec, $request->has('onlyPosters'), $request->get('frameSec')
         )));
      }
      return 'yes';
   }

   public function destroy (int $id, Request $request)
   {
      $this->init();

//        $videoLessons = File::allFiles('video/lessons/');
//        $destroyLesson = preg_grep("/n{$id}_/", $videoLessons);
//        File::delete($destroyLesson);
      $topic = Topic::findOrFail($id);
      $removeFiles = new RemoveFiles($topic);
      $removeFiles->RemoveDirectory();

//        (new UpdateTotalCount())->updateTotalLessons();
//        (new UpdateTotalCount())->updateTotalTopics($id);
      (new UpdateTotalCount())->updateTotalLessons();
      (new UpdateTotalCount())->updateTotalTopics();
      (new UpdateTotalCount())->updateTotalDuration();

      return $this->delete($id, $request);
   }

   protected function checkDrop ($item): bool {
      return true;
   }
}
