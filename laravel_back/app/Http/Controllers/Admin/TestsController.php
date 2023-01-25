<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use App\Test;
use App\Course;
use App\Facades\Uploader;
class TestsController extends AdminController
{

    protected $model;

    public function __construct ()
    {
        parent::__construct();
    }

    public function init ()
    {
        $this->setPage([
            'route' => 'admin.tests',
            'title' => 'Tests - [ ADMIN ]',
            'h1'    => 'Tests'
        ]);

        $this->setModel(Test::class);
        $this->setForm();
        $this->updateForm();
        $this->setRules();
    }

    private function updateForm (): void
    {
        $course = Course::pluck('name', 'id')->toArray();
        $status = ['editing'=>'editing','published'=>'published'];

        $this->setForm('course_id.items', $course);
        $this->setForm('status.items', $status);
    }

    public function index(Request $request): Renderable
    {
        $this->init();

        $items = $this->model::orderBy('id', 'desc')
            ->paginate(20);

        $data = [
            'page'  => $this->getPage(),
            'items' => $items,
        ];

        return view('admin._list', $data);
    }


    public function create(): Renderable
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
        $request->validate($this->rules);
        $item = Uploader::create($this->model);

        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Добавлено. ID: <b>{$item->id}</b>");

    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
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

    public function update(int $id, Request $request)
    {
        $this->init();
        $this->setRule('course_id.unique', "tests,course_id,{$id}");
        $request->validate($this->rules);

        $item = $this->model::findOrFail($id);
        $item->fill($request->all());
        $item->save();

         return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Update. ID: <b>{$item->id}</b>");
    }


    public function destroy($id, Request $request)
    {
        $this->init();
        return $this->delete($id, $request);
    }

    protected function checkDrop ($item): bool {
        return true;
    }
}
