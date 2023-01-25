<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Route;
use File;

abstract class AdminController extends Controller
{
	public $page = [];
	public $menu = [];
	protected $model;
	protected $form;
	protected $rules;

	public function __construct () {}

	/*
	Set model
	*/
	protected function setModel ($model): void
	{
		$this->model = $model;
	}

	protected function checkStore (): bool {
		return true;
	}

	protected function checkUpdate ($item): bool {
		return true;
	}

	protected function checkDrop ($item): bool {
		return true;
	}


	public function index (Request $request)
	{
		$items = $this->model::orderBy('id', 'desc')
		->get();

		return $items;
	}


	public function show ($uniqueField, Request $request)
	{
		$item = $this->model::findOrFail($uniqueField);

		return $item;
	}


	public function store (Request $request)
	{
		if($this->checkStore()) {
			$request->validate($this->rules);

			$item = $this->model::create($request->all());

			return $item;
		}

		return response()->json(['error' => 'Creating items in this class is forbidden'], 403);
	}

	protected function preUpdate (int $id, Request $request) {
	}


	public function update (int $id, Request $request)
	{
		$item = $this->model::findOrFail($id);
		if($this->checkUpdate($item)) {
			$this->preUpdate($id, $request);
			$request->validate($this->rules);

			$item->fill($request->all());
			$item->save();

			return $item;
		}

		return response()->json(['error' => 'Editing items in this class is forbidden'], 403);
	}


	public function destroy (int $id, Request $request)
	{
		$item = $this->model::findOrFail($id);
		if($this->checkDrop($item)) {
			$item->delete();
			return null;
		}

        return response()->json(['error' => 'Deleting this item is forbidden'], 403);
	}

	/*
	Set validate rules for store and update
	Set rules from config('[ROUTE].rules')
	/config/admin/[ROUTE-2nd].php
	*/
	protected function setRules (): void
	{
		$this->rules = config("{$this->page['route']}.rules", []);
	}

	/*
	Set rule for dynamic parameters
	*/
	protected function setRule (string $field, string $update = null): void
	{
		if(!preg_match('/\./', $field)) {
			$this->rules[$field] = $update;
			return ;
		}
		self::setDefinitionRule($field, $update);
	}

	private function setDefinitionRule (string $field, string $update = null): void
	{
		list($field, $key) = explode('.', $field);
		$lines = explode('|', $this->rules[$field]);
		$rules = [];
		foreach ($lines as $i => $value) {
			$line = explode(':', $value);
			$rule = $line[0];
			$set = count($line) == 1 ? null : $line[1];

			if($rule == $key) {
				if(is_null($update)) {
					continue;
				}
				elseif(is_null($set)) {
					$rule = $update;
				}
				else {
					$set = $update;
				}
			}
			$rules[$rule] = $set;
		}

		$current = [];
		foreach ($rules as $key => $value) {
			$current[] = is_null($value) ? $key : "{$key}:{$value}";
		}

		$this->rules[$field] = implode('|', $current);
	}

	// protected function getFiles (object $item, string $field): array
	// {
	// 	$files = [];
	// 	$folder = $item::FILES[$field];
	// 	$value = $item[$field];
	// 	if(is_string($value)) {
	// 		$files = [
	// 			call_user_func([ $item, $field ])
	// 		];
	// 	}
	// 	foreach ($files as $key => $file) {
	// 		if(!File::exists($file)) {
	// 			unset($files[$key]);
	// 			continue;
	// 		}
	// 		$files[$key] = (object) [
	// 			'src'  => $file,
	// 			'mime' => mime_content_type($file),
	// 		];
	// 	}
	// 	return $files;
	// }
}
