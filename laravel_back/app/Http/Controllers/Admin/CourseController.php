<?php

namespace App\Http\Controllers\Admin;

use App\Classes\RemoveFiles;
use App\Classes\UpdateTotalCount;
use App\Course;
use App\Facades\Uploader;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class CourseController extends AdminController
{
   protected $model;

   public function __construct ()
   {
      parent::__construct();
   }

   public function init ()
   {
      $this->setPage([
         'route' => 'admin.courses',
         'title' => 'Courses - [ ADMIN ]',
         'h1'    => 'Courses'
      ]);

      $this->setModel(Course::class);
      $this->setForm();
      $this->setRules();
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

      (new UpdateTotalCount())->updateTotalLessons();
      (new UpdateTotalCount())->updateTotalTopics();

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
      $this->setForm('promo_video_url.items', $this->getFiles(
         $item,
         'promo_video_url',
         $item->promo_video_available_formats ? [$item->promo_video_available_formats[0]] : []
      ));

      $this->setForm('order.item', $item->order);

      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
         'item' => $item,
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
      $request->merge([
         'name' => Str::slug($request->title)
      ]);
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
      $this->setRule('route.unique', "courses,route,{$id}");
      $this->setRule('name.unique', "courses,name,{$id}");
      $this->prepareRequest($request);
      $request->validate($this->rules);
      $item = $this->model::findOrFail($id);

      Uploader::update($item);

      (new UpdateTotalCount())->updateTotalLessons();
      (new UpdateTotalCount())->updateTotalTopics();

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Updated: <b>{$item->title}</b>");
   }

   public function fastUpdate(int $id, Request $request)
   {
      $this->init();
      $item = $this->model::findOrFail($id);
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

   public function destroy (int $id, Request $request)
   {
      $this->init();

      $removeFiles = new RemoveFiles(Course::find($id));
      $removeFiles->RemoveDirectory();

      (new UpdateTotalCount())->updateTotalLessons();
      (new UpdateTotalCount())->updateTotalTopics();
      (new UpdateTotalCount())->updateTotalDuration();

      return $this->delete($id, $request);
   }

   protected function checkDrop ($item): bool {
      return true;
   }
}
