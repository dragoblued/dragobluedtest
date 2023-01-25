<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

use App\Page;
use Illuminate\Support\Facades\Validator;

class PageController extends AdminController
{
	protected $model;

	public function __construct ()
	{
		parent::__construct();
	}

	public function init () {
		$this->setPage([
			'route' => 'admin.pages',
			'title' => 'Pages - [ ADMIN ]',
			'h1'    => 'Pages',
         'func'  => []
		]);
		$this->setModel(Page::class);
		$this->setForm();
		$this->setRules();
	}

	public function index (Request $request): Renderable
	{
		$this->init();
		$items = $this->model::orderBy('route')
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

		$item = $this->model::create($request->all());

		return redirect()
		->route("{$this->page['route']}.index")
		->with('alert', "Added. ID: <b>{$item->id}</b>");
	}

	public function edit (int $id): Renderable
	{
		$this->init();
		$this->setCurrent('edit');

		$item = $this->model::findOrFail($id);

		$data = [
			'page' => $this->getPage(),
			'form' => $this->getForm(),
			'item' => $item,
		];
		return view('admin._form', $data);
	}

	public function update (int $id, Request $request): RedirectResponse
	{
		$this->init();
		$this->setRule('route.unique', "pages,route,{$id}");
		$request->validate($this->rules);

		$item = $this->model::findOrFail($id);
		$item->fill($request->all());
		$item->save();

		return redirect()
		->route("{$this->page['route']}.index")
		->with('alert', "Edited. ID: <b>{$item->id}</b>");
	}

	public function destroy (int $id, Request $request)
	{
		$this->init();
		return $this->delete($id, $request);
	}

	protected function checkDrop ($item): bool {
		return true;
	}
}
