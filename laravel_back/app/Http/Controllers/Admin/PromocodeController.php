<?php

namespace App\Http\Controllers\Admin;

use App\Course;
use App\Event;
use App\Promocode;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromocodeController extends AdminController
{
   protected $model;

   public function __construct ()
   {
      parent::__construct();
   }

   public function init ()
   {
      $this->setPage([
         'route' => 'admin.promocodes',
         'title' => 'Promocodes - [ ADMIN ]',
         'h1'    => 'Promocodes'
      ]);
      $this->setModel(Promocode::class);
      $this->rules = [
         'code'     => 'required|unique:promocodes,code',
         'discount' => 'nullable|numeric|min:1',
         'usage_limit' => 'nullable|numeric|min:1',
         'subject_type' => 'nullable|string|max:255',
         'subject_id' => 'required_if:subject_type,!=,null'
      ];
   }

   public function index(Request $request)
   {
      $this->init();
      $items = $this->model::orderBy('id', 'desc')
         ->paginate(20);

      $data = [
         'page'  => $this->getPage(),
         'items' => $items,
         'currencySign' => '€'
      ];
      return view('admin._list', $data);
   }

   public function show(int $id)
   {
      $this->init();
      $item = $this->model::with(['subject'])->findOrFail($id);
      return response()->json($item);
   }

   /**
    * Show the form for creating a new resource.
    *
    */
   public function create()
   {
      $this->init();
      $this->setForm();
      $this->setCurrent('create');

      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm()
      ];
      return view('admin._form', $data);
   }


   protected function preUpdate($id, Request $request)
   {
      $dateStart = substr($request->get('daterange'), 0,10);
      $dateEnd = substr($request->get('daterange'), -10);

      $request->merge([
         "start_at" => (new Carbon($dateStart))->format('Y-m-d'),
         "end_at" => (new Carbon($dateEnd))->format('Y-m-d'),
         "subject_id" => $request->get('subject_type') ? $request->get('subject_id') : null
      ]);
   }

   private function checkDiscount(Request $request): array
   {
      $checking = ['valid' => true, 'messages' => []];
      $type = $request->get('discount_type');
      $value = $request->get('discount');
      if ($type === 'percent' && ($value > 100 || $value < 1)) {
         $checking['valid'] = false;
         $checking['messages'] = ['Discount percents must be between 1 and 100'];
      }
      return $checking;
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\RedirectResponse
    */
   public function store (Request $request)
   {
      $request['code'] = strtoupper($request->code);
      $this->init();
      $request->validate($this->rules);
      $this->preUpdate(null, $request);
      $checkDiscount = $this->checkDiscount($request);
      if (!$checkDiscount['valid']) {
         return redirect()
            ->back()
            ->withInput($request->input())
            ->withErrors($checkDiscount['messages']);
      }

      $item = $this->model::create($request->all());

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Added. ID: <b>{$item->id}</b>");
   }

   public function edit(int $id)
   {
      $this->init();
      $this->setForm();
      $this->setCurrent('edit');
      $item = $this->model::findOrFail($id);
      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
         'item' => $item
      ];

      return view('admin._form', $data);
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\RedirectResponse
    */
   public function update(int $id, Request $request)
   {
      $this->init();
      $this->setRule('code.unique', "promocodes,code,{$id}");
      $request->validate($this->rules);
      $this->preUpdate($id, $request);
      $checkDiscount = $this->checkDiscount($request);
      if (!$checkDiscount['valid']) {
         return redirect()
            ->back()
            ->withInput($request->input())
            ->withErrors($checkDiscount['messages']);
      }

      $item = $this->model::findOrFail($id);
      $item->fill($request->all());
      $item->save();

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Updated. ID: <b>{$item->id}</b>");
   }

   public function destroy(int $id, Request $request)
   {
      $this->init();
      return $this->delete($id, $request);
   }

   public function generate(): string
   {
      $newCode = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
      $tryCount = 0;
      while (Promocode::where('code', $newCode)->first() !== null && $tryCount < 100) {
         $newCode = substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
         $tryCount++;
      }
      return $newCode;
   }

   public function getItems(string $type): JsonResponse
   {
      $items = [];
      switch ($type) {
         case 'Course':
            $items = Course::orderBy('id')->get();
            break;
         case 'Topic':
            $items = Course::orderBy('id')
               ->with('topics')
               ->get(['id', 'name'])
               ->mapWithKeys(function ($item) {
                  return [
                     $item['name'] => $item
                        ->topics
                  ];
               });
            break;
         case 'Event':
            $items = Event::orderBy('id')->get();
            break;
         default:
            break;
      }
      return response()->json($items);
   }
}
