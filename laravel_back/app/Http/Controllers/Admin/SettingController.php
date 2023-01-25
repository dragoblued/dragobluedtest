<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Setting;

class SettingController extends AdminController
{
    protected $model;

    public function __construct ()
    {
        parent::__construct();
    }

    public function init () {
        $this->setPage([
            'route' => 'admin.settings',
            'title' => 'System parameters - [ ADMIN ]',
            'h1'    => 'System parameters'
        ]);
        $this->setModel(Setting::class);
        $this->setForm();
        /*$this->updateForm();*/
        $this->setRules();
    }

    public function index(Request $request)
    {
        $this->init();

        $items = $this->model::get()
        ->mapWithKeys(function($item) {
            return [$item->key => $item->value];
        })
        ->toArray();

        $items['currency'] = json_decode($items['currency']);
        $items['social_links'] = json_decode($items['social_links']);
        $page = $this->getPage();
        $page->func = [];

        $data = [
            'page'  => $page,
            'items' => $items,
        ];
        return view('admin.settings', $data);
    }


    public function currency(){
         $this->init();
         $this->setPage([
            'route' => 'admin.currency',
            'title' => 'Currency - [ ADMIN ]',
            'h1'    => 'Currency'
        ]);
        $page = $this->getPage();
        $page->func = [];

        $item = $this->model::where('key','currency')->first()->toArray();
        $item = json_decode($item['value']);

        $data = [
            'page'  => $page,
            'items' => $item,
        ];
        return view('admin.currency', $data);
    }

    public function updateCurrency(Request $request){

            $currency = [];
            foreach ($request->code as $k => $v) {
                if ($request->currency === $v)
                   $selected = true;
                else
                    $selected = false;
               $sign = $request->sign[$k];
               $name = $request->name[$k];
               $currency[] = ["code"=>$v,'name'=>$name,'sign'=>$sign,'selected' => $selected];
            }
            $currency = json_encode($currency,JSON_UNESCAPED_UNICODE);
            $item = Setting::where('key','currency')->first();
            $item->value = $currency;
            $item->save();

            return back()
             ->with('flash_message', 'Обновлено');
    }


    public function address(){
        $this->init();
         $this->setPage([
            'route' => 'admin.address',
            'title' => 'Address - [ ADMIN ]',
            'h1'    => 'Address'
        ]);
        $page = $this->getPage();
        $page->func = [];

        $items = $this->model::where('key','location_coordinates')
                    ->orWhere('key','location_url')
                    ->orWhere('key','address')
                    ->orWhere('key','address_building_name')
                    ->get();

        $coords = $this->model::where('key','location_coordinates')->first();

        $data = [
            'page'  => $page,
            'items' => $items,
            'coords' => $coords ? $coords->value : null
        ];
        return view('admin.address', $data);

    }

    public function addressUpdate(Request $request){
        $this->init();

        $request->merge([
            'location_coordinates' => $request->location_coordinates
        ]);

        foreach ($request->except('_method', '_token') as $key => $value) {
            $item = $this->model::where('key', $key)->first();
            if ($item) {
                $item->fill(['value' => $value]);
                $item->save();
            }
        }
        return back()->with('alert', 'Updated');

    }

    public function preUpdate($id, Request $request)
    {
       $request->merge([
          'is_payment_enabled' => !is_null($request->get('is_payment_enabled'))
       ]);
       return $request;
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
//        $request->validate([
//            'title' => 'required',
//            'email' => 'email|required',
//            'phone' => 'integer|required',
//            'copyright_text' => 'required',
//            'about_us' => 'required',
//            'privacy_policy' => 'required'
//        ]);
       $request = $this->preUpdate($id, $request);
        foreach ($request->except('_method', '_token') as $key => $value) {
            $item = $this->model::where('key', $key)->first();
            if ($item) {
                $item->fill(['value' => $value]);
                $item->save();
            } else {
                $item = new Setting();
                $item->key = $key;
                $item->value = $value;
                $item->save();
            }
        }
        return back()->with('alert', 'Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id, Request $request)
    {
        //
    }


    protected function checkDrop ($item): bool {
        return true;
    }
}
