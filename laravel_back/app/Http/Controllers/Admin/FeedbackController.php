<?php

namespace App\Http\Controllers\Admin;

use App\Feedback;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

class FeedbackController extends AdminController
{
    protected $model;

    public function __construct ()
    {
        parent::__construct();
    }

    public function init ()
    {
        $this->setPage([
            'route' => 'admin.feedback',
            'title' => 'Feedback - [ ADMIN ]',
            'h1'    => 'Feedback',
            'func'  => ['delete']
        ]);

        $this->setModel(Feedback::class);
//        $this->setForm();
//        $this->setRules();
    }

    public function index (Request $request): Renderable
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
}
