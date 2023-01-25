<?php

namespace App\Http\Controllers\Api\Admin;

use App\Test;
use Illuminate\Http\Request;

class TestController extends AdminController
{
    public function __construct ()
    {
        $this->model = Test::class;
        $this->rules = [];
    }

    public function findByCourseId($courseId)
    {
        return $this->model::with(['questions'])
            ->where('course_id', $courseId)
            ->firstOrFail();

    }
}
