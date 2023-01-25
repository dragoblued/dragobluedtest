<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Gallery;
use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;

use App\Page;

class PageEventsController extends AdminController
{
	protected $model;

	public function __construct ()
	{
		parent::__construct();
	}

	public function init () {
		$this->setPage([
			'route' => 'admin.page-events',
			'title' => 'Page Live-courses - [ ADMIN ]',
			'h1'    => 'Page Live-courses',
		]);
		$this->setModel(Page::class);
		$this->setForm();
		$this->setRules();
	}

   /**
    * @param Request $request
    * @return RedirectResponse
    */
   public function index(Request $request): RedirectResponse
   {
      $this->init();

      return redirect()->route('admin.page-events.edit', ['id' => 3]);
   }

   /**
    * @param int $id
    * @return Renderable
    */
   public function edit (int $id): Renderable
   {
      $this->init();
      $this->setCurrent('edit');

      $item = $this->model::findOrFail($id);

      $item->content = json_decode($item->content, true);

      $data = [
         'page' => $this->getPage(),
         'item' => $item,
         'gallery' => $item->gallery()->orderBy('order')->get(),
         'headerVideoItem' => isset($item->content['header_video_url']) ? Gallery::find($item->content['header_video_url']) : null
      ];

      return view('admin.pages.page-events', $data);
   }

   public function updateGallery($data, $item) {
      if (isset($data['gallery'])) {
         $item->gallery()->detach();
         $galleryArr = json_decode($data['gallery']);
         foreach ($galleryArr as $index => $id) {
            $item->gallery()->sync([3 => [
               'gallery_id' => $id,
               'order' => $index,
               'created_at' => now()
            ]], false);
         }
      }
   }

   /**
    * @param int $id
    * @param Request $request
    * @return RedirectResponse
    */
   public function update (int $id, Request $request): RedirectResponse
   {
      $this->init();
      $item = $this->model::findOrFail($id);
      $this->updateGallery($request->all(), $item);
      $data = $request->except(['_token', '_method', 'gallery']);
      if (count($data) > 0) {
         $item->content = json_encode((object) $data, JSON_UNESCAPED_UNICODE);
      }
      $item->save();
      return back()->with('alert', "Updated");
   }
}
