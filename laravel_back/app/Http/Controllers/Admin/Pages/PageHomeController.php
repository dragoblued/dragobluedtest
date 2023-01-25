<?php

namespace App\Http\Controllers\Admin\Pages;

use App\Gallery;
use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;

use App\Page;

class PageHomeController extends AdminController
{
	protected $model;

	public function __construct ()
	{
		parent::__construct();
	}

	public function init () {
		$this->setPage([
			'route' => 'admin.page-home',
			'title' => 'Page Main - [ ADMIN ]',
			'h1'    => 'Page Main',
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

      return redirect()->route('admin.page-home.edit', ['id' => 1]);
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
         'middleVideoItem' => isset($item->content['middle_video_url']) ? Gallery::find($item->content['middle_video_url']) : null
      ];

      return view('admin.pages.page-home', $data);
   }

   /**
    * @param int $id
    * @param Request $request
    * @return RedirectResponse
    */
   public function update (int $id, Request $request): RedirectResponse
   {
      $this->init();
      $data = $request->except(['_token', '_method']);
      $item = $this->model::findOrFail($id);
      if (count($data) > 0) {
         $item->content = json_encode((object) $data, JSON_UNESCAPED_UNICODE);
      }
      $item->save();
      return back()->with('alert', "Updated");
   }
}
