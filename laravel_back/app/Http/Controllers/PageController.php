<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Renderable;

use App\Page;

class PageController extends SiteController
{
	public function index (Request $request, $id = null): Renderable
	{
		$this->setPage(Page::settings('main'));
		$data = [
			'page'   => $this->page
		];
		return view('site.index', $data);
	}

    public function chat (Request $request): Renderable
    {
        $this->setPage(Page::settings('main'));
        $data = [
            'page'   => $this->page
        ];
        return view('site.chat', $data);
    }
}
