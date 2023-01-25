<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use App\Setting;
class SocialLinksController extends AdminController
{
    protected $model;

    public function __construct ()
    {
        parent::__construct();
    }

    public function init () {
        $this->setPage([
            'route' => 'admin.social_links',
            'title' => 'Social links - [ ADMIN ]',
            'h1'    => 'Social links'
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

        $items['social_links'] = json_decode($items['social_links']);
        $social_links = [];
        foreach ($items['social_links'] as $key => $value) {
           $value->icon = '<i class="fab fa-'.$value->icon.'"></i>';
           $social_links[] = $value;
        }

        $page = $this->getPage();

        $data = [
            'page'  => $page,
            'items' => $social_links,
        ];

        return view('admin._list', $data);
    }

    public function create()
    {
        $this->init();
        $this->setCurrent('create');

        $data = [
            'page' => $this->getPage(),
            'form' => $this->getForm(),
        ];
        return view('admin._form', $data);
    }

    public function store(Request $request)
    {
        $this->init();
        $this->setRules();
        $request->validate($this->rules);

        $item = Setting::where('key','social_links')->first();
        $item['value'] = json_decode($item['value']);

        $id = count($item['value'])+1;
        $id = (string)$id;

        $new_item = [
            'id'=>$id,
            'name'=>$request->name,
            'url'=>$request->url,
            'icon'=>$request->icon
        ];
        $new_item = (object)$new_item;

        $arr = [];
        foreach ($item['value'] as $key => $value) {
            $arr[] = $value;
        }

        array_push($arr, $new_item);
        $item['value'] = json_encode($arr);
        $item->save();


        return redirect()
        ->route('admin.social_links.index')
        ->with('flash_message', 'Обновлено');
    }

    public function edit(int $id)
    {
        $this->init();
        $this->setCurrent('edit');

        $items = $this->model::get()
        ->mapWithKeys(function($item) {
            return [$item->key => $item->value];
        })
        ->toArray();

        $items['social_links'] = json_decode($items['social_links']);
        foreach ($items['social_links'] as $key => $value) {
            if ($value->id == $id) {
                $item = (array)$value;
            }
        }

        $data = [
            'page' => $this->getPage(),
            'form' => $this->getForm(),
            'item' => $item,
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
        $this->setRules();
        $request->validate($this->rules);

        $item = Setting::where('key','social_links')->first();
        $item['value'] = json_decode($item['value']);

        $new_item = [];
        foreach ($item['value'] as $key => $value) {
            if ($value->id == $id) {
               $value->name = $request->name;
               $value->url = $request->url;
               $value->icon = $request->icon;
            }
            $new_item[] = $value;
        }

        $item['value'] = json_encode($new_item);
        $item->save();

        return redirect()
        ->route('admin.social_links.index')
        ->with('flash_message', 'Обновлено');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id, Request $request)
    {
        $item = Setting::where('key','social_links')->first();
        $item['value'] = json_decode($item['value']);
        
        $filter = [];
        $filter = array_filter($item['value'], function($v) use ($id){
            return $v->id != $id;
        });

        $new_item = [];
        foreach ($filter as $key => $value) {
           $new_item[] = $value;
        }
        $item['value'] = json_encode($new_item);
        $item->save();
        return [$id, $request];
    }

    protected function checkDrop ($item): bool {
        return true;
    }
}
