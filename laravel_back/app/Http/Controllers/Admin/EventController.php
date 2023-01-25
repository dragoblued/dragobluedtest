<?php

namespace App\Http\Controllers\Admin;

use App\Classes\RemoveFiles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;

use Carbon\Carbon;
use App\Setting;
use App\Event;
use App\Facades\Uploader;
use Illuminate\Support\Str;

class EventController extends AdminController
{

	protected $model;

	public function __construct ()
	{
		parent::__construct();
	}

	public function init ()
	{
		$this->setPage([
			'route' => 'admin.events',
			'title' => 'Events - [ ADMIN ]',
			'h1'    => 'Events'
		]);

		$this->setModel(Event::class);
		$this->setForm();
		$this->updateForm();
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
         'ext_scripts' => [
            '<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>',
            '<script src="'.config('app.url').'/js/inc/video-hls.js"></script>'
         ]
		];
		return view('admin._form', $data);
	}


	public function store(Request $request)
	{
		$this->init();
    //   $request->merge([
    //      'name' => Str::slug($request->title)
    //   ]);
		$request->validate($this->rules);
		$request = $this->preUpdated($request);

		$item = Uploader::create($this->model);
      $this->updateGallery($request->all(), $item);
		return redirect()
			->route("{$this->page['route']}.index")
			->with('alert', "Created: <b>{$item->title}</b>");
	}


	public function preUpdated($request){

		$program = $this->programConverting($request);

		$request->merge([
			'program' => $program,
			'is_model_visible' => !is_null($request->get('is_model_visible')),
			'address_coordinates' => json_decode($request->get('address_coordinates')),
		]);
		return $request;
	}

	private function programConverting($request){
		$arr = $request->toArray();
		$items = [];
		$i = 1;

		foreach ($arr as $key => $value) {
			if (isset($arr['start'.$i])) {
				$items[] = [
					'start' => $arr["start".$i],
					'end' => $arr["end".$i],
					'label' => $arr['label'.$i]
				];
			}
			$i++;
		}

		$json = function($start, $end, $label){
			return [
				'start'=> $start,
				'end'=> $end,
				'label'=> $label
			];
		};

		$program = [];
		foreach ($items as $key => $value) {
			$program[] = array_map($json,$value['start'],$value['end'], $value['label']);
		}

		return $program;
	}

	public function show($uniqueField, Request $request)
	{
		$item = Event::findOrFail($uniqueField);
		return $item;
	}

	public function edit(int $id): Renderable
	{

		$this->init();
		$this->setCurrent('edit');
		$currency_icon = $this->getCurrencyIcon();
		$item = $this->model::findOrFail($id);
		$this->setForm('poster_url.items', $this->getFiles($item, 'poster_url', ['min', '']));
		$this->setForm('model_url.items', $this->getFiles($item, 'model_url'));
		$this->setForm('collage_url.items', $this->getFiles($item, 'collage_url', ['min', '']));
//		$this->setForm('promo_video_url.items', $this->getFiles(
//		   $item,
//         'promo_video_url',
//         $item->promo_video_available_formats ? [$item->promo_video_available_formats[0]] : []
//      ));
		$data = [
			'page' => $this->getPage(),
			'form' => $this->getForm(),
			'item' => $item,
			'currency_icon' => $currency_icon,
         'gallery' => $item->gallery()->orderBy('order')->get(),
         'ext_scripts' => [
            '<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>',
            '<script src="'.config('app.url').'/js/inc/video-hls.js"></script>'
         ]
		];

		return view('admin._form', $data);
	}

   public function updateGallery($data, $item) {
      if (isset($data['gallery'])) {
         $item->gallery()->detach();
         $galleryArr = json_decode($data['gallery']);
         foreach ($galleryArr as $index => $id) {
            $item->gallery()->sync([$item->id => [
               'gallery_id' => $id,
               'order' => $index,
               'created_at' => now()
            ]], false);
         }
      }
   }

	public function update(int $id, Request $request): RedirectResponse
	{
		$this->init();
		$this->setRule('route.unique', "events,route,{$id}");
		$this->setRule('name.unique', "events,name,{$id}");
		$request->validate($this->rules);
		$request = $this->preUpdated($request);

		$item = $this->model::findOrFail($id);
      $this->updateGallery($request->all(), $item);
		Uploader::update($item);
		$item->save();

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

	public function destroy(int $id, Request $request)
	{
		$this->init();

		$removeFiles = new RemoveFiles(Event::find($id));
		$removeFiles->RemoveDirectory();

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
