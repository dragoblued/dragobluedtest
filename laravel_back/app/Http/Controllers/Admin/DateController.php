<?php

namespace App\Http\Controllers\Admin;

use App\Classes\UpdateTotalCount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Date;
use App\Event;
class DateController extends AdminController
{
   protected $model;

   public function __construct ()
   {
      parent::__construct();
   }

   public function init ()
   {
      $this->setPage([
         'route' => 'admin.dates',
         'title' => 'Dates - [ ADMIN ]',
         'h1'    => 'Dates'
      ]);

      $this->setModel(Date::class);
      $this->setForm();
      $this->setRules();
   }

   public function index(Request $request)
   {
      $this->init();

      $this->checkSeats();

      $items = $this->model::orderBy('start', 'desc')
         ->with(['event'])
         ->paginate(20);

      $data = [
         'page'  => $this->getPage(),
         'items' => $items
      ];
      return view('admin.dates', $data);
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {
      $this->init();
      $this->setCurrent('create');
      $events = Event::orderBy('id')->pluck('name', 'id')->toArray();

      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
         'events' => $events,
      ];
      return view('admin._form', $data);
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {
      $this->init();
      $request->validate($this->rules);

      $dateStart = substr($request->daterange,0,10);
      $dateEnd = substr($request->daterange,-10);

      $request->merge([
         "start" => (new Carbon($dateStart))->format('Y-m-d'),
         "end" => (new Carbon($dateEnd))->format('Y-m-d'),
         "seats_vacant" => $request->get('seats_total'),
      ]);
      // dd($request);
      $item = $this->model::create($request->all());

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Added. ID: <b>{$item->id}</b>");
   }

   public function edit(int $id)
   {
      $this->init();
      $this->setCurrent('edit');
      $item = $this->model::findOrFail($id);
      $events = Event::orderBy('id')->pluck('name', 'id')->toArray();
      $data = [
         'page' => $this->getPage(),
         'form' => $this->getForm(),
         'item' => $item,
         'events' => $events,
      ];

      return view('admin._form', $data);
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(int $id, Request $request)
   {
      $this->init();

      $request->validate($this->rules);

      $dateStart = substr($request->daterange,0,10);
      $dateEnd = substr($request->daterange,-10);

      $request->merge([
         "start" => (new Carbon($dateStart))->format('Y-m-d'),
         "end" => (new Carbon($dateEnd))->format('Y-m-d')
      ]);

      $item = $this->model::findOrFail($id);
      $item->fill($request->all());
      $item->save();

      (new UpdateTotalCount())->updateDateSeats($item->id);

      return redirect()
         ->route("{$this->page['route']}.index")
         ->with('alert', "Updated. ID: <b>{$item->id}</b>");
   }

   public function destroy(int $id, Request $request)
   {
      $this->init();
      return $this->delete($id, $request);
   }

   public function getCustomersList (int $id, string $type): JsonResponse
   {
      $date = Date::findOrFail($id);

      if ($type === 'purchased') {
         $users = $date->users()
            ->wherePivot('is_purchased', 1)
            ->get();
      } else {
         $users = $date->users()
            ->wherePivot('is_purchased', '!=', 1)
            ->wherePivot('is_canceled', '!=', 1)
            ->get();
      }

      return response()->json($users);
   }

   public function checkSeats() {
      $dates = Date::where('is_expired', '!=', 1)->get();
      foreach ($dates as $date) {
         (new UpdateTotalCount())->updateDateSeats($date->id);
      }
      return true;
   }
}
