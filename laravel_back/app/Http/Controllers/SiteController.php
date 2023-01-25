<?php

namespace App\Http\Controllers;

use App\Page;
use App\Setting;

class SiteController extends Controller
{
	public $page;

	protected function setPage (Page $page): void
	{
		$page['settings'] = Setting::get();
		$this->page = $page;
	}
}
