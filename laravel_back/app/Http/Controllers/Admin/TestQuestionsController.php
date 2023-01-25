<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Support\Renderable;
use App\Test;
use App\TestQuestion;
class TestQuestionsController extends AdminController
{
    protected $model;

    public function __construct ()
    {
        parent::__construct();
    }

    public function init ()
    {
        $this->setPage([
            'route' => 'admin.test_questions',
            'title' => 'Test questions - [ ADMIN ]',
            'h1'    => 'Test questions'
        ]);

        $this->setModel(TestQuestion::class);
        $this->setForm();
        $this->updateForm();
        $this->setRules();
    }

    private function updateForm (): void
    {
        $test = Test::pluck('title', 'id')->toArray();
        $types = [
            'single-choice'=>'single-choice',
            'multiple-choice'=>'multiple-choice',
            'fill-in-the-blanks' => 'fill-in-the-blanks'
        ];

        $this->setForm('test_id.items', $test);
        $this->setForm('type.items', $types);
    }

    public function index(Request $request): Renderable
    {
        $this->init();
        //$items->test->title
        $items = $this->model::orderBy('test_id', 'desc')
            ->paginate(20);

        $data = [
            'page'  => $this->getPage(),
            'items' => $items,
        ];

        return view('admin._list', $data);
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

        $data = [
            'page' => $this->getPage(),
            'form' => $this->getForm(),
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
        $request = $this->preUpdated($request);
        $test = Test::find($request->test_id);
        $test->total_mark += $request->mark;
        $test->save();
        $item = $this->model::create($request->all());

        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Add. ID: <b>{$item->id}</b>");
    }


    private function preUpdated($request){
        if ($request->type === 'fill-in-the-blanks') {
            $request->merge([
                //'correct_answers' => json_encode($request->correct_answers),
            ]);
        }else{
            $request->merge([
                'options' => json_encode($request->options),
                //'correct_answers' => json_encode($request->correct_answers),
            ]);
        }
        return $request;
    }

    public function edit(int $id)
    {
        $this->init();
        $this->setCurrent('edit');

        $item = $this->model::findOrFail($id);

        if ($item->type !== 'fill-in-the-blanks') {
            $item->options = json_decode($item->options);

        }

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
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        $this->init();
        $request->validate($this->rules);
        $request = $this->preUpdated($request);

        $item = $this->model::findOrFail($id);

        $test = Test::find($request->test_id);
        if ($item->mark > $request->mark) {
            $res = $item->mark - $request->mark;
            $test->total_mark -= $res;
        }else{
            $res = $item->mark - $request->mark;
            $test->total_mark += $res;
        }

        $test->save();
        $item->fill($request->all());
        $item->save();

         return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Update. ID: <b>{$item->id}</b>");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $this->init();
        return $this->delete($id, $request);
    }

    protected function checkDrop ($item): bool {
        return true;
    }
}
