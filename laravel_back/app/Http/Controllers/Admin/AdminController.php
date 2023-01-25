<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Room;
use Auth;
use Illuminate\Http\Request;
use Route;
use File;
use Gate;

abstract class AdminController extends Controller
{
	public $page = [];
	public $menu = [];
	protected $model;
	protected $form;
	protected $rules;

	public function __construct ()
	{
	    // dd(auth()->user());
		// $this->createMenu();

		// $this->middleware(function ($request, $next) {
		// 	$this->user = auth()->user();
		// 	return $next($request);
		// });

		/*
		U should aware
		Because inline middleware whyta didnt work for me
		I make init method for children controllers
		Its not satisfieble, but a havent enough time for this problem
		and if you have some time please try correct this
		*/
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

	/*
	Set model
	*/
	protected function setModel ($model): void
	{
		$this->model = $model;
	}

	/*
	Set form for create and edit
	Set form template from config('[ROUTE].form')
	/config/admin/[ROUTE-2nd].php
	*/
	protected function setForm (string $line = null, $values = null): void
	{
		if(is_null($line) && is_null($values)) {
			$this->form = config("{$this->page['route']}.form", []);
			return ;
		}

		list($field, $property) = explode('.', $line);
		$this->form[$field][$property] = $values;
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

	/*
	Set current page route
	Set tag Title
	Set tag H1
	Set available functions
	*/
	protected function setPage (array $settings): void
	{
		//Проверка прав
		if(Gate::denies('ADMIN_VIEW_MAIN')) {
			abort(403);
		}

		$this->createMenu();

		$this->page = $settings;
		if($settings['route'] == 'admin.index' || array_key_exists('func', $settings)) {
			return ;
		}

		$func = [];
		// Проверки прав
		if (Gate::allows('ADMIN_CREATE')) {
			array_push($func, 'create');
		}
		if (Gate::allows('ADMIN_EDIT')) {
			array_push($func, 'edit');
		}
		if (Gate::allows('ADMIN_DELETE')) {
			array_push($func, 'delete');
		}

		$this->page['func'] = $func;
	}

	/*
	Get page and set variables
	Set admin user
	Set menu from config('admin._menu')
	/config/admin/_menu.php
	*/
	protected function getPage ()
	{
		if(Gate::denies('ADMIN_VIEW_MAIN')) {
			abort(403);
		}

		$settings = [
			'admin' => Auth::user(),
			'menu' => $this->menu,
		];
		$this->page = array_merge($settings, $this->page);

		return (object) $this->page;
	}

	protected function setH1 (string $h1, bool $full = false): void
	{
		if($full) {
			$this->page['h1'] = $h1;
		}
		else {
			$this->page['h1'] .= $h1;
		}
	}

	protected function setCurrent (string $func): void
	{
		$this->page['current'] = $func;

		if($func == 'create') {
			$this->setH1(': create');
		}
		if($func == 'edit') {
			$this->setH1(': edit');
		}
		if($func == 'delete') {
			$this->setH1(': remove');
		}
	}

	/*
	Get generated form tamplate from config
	*/
	protected function getForm (): array
	{
		$fields = $this->form;

		foreach ($fields as $key => $field) {
			// Css
			if(!array_key_exists('class', $field)) {
				$fields[$key]['class'] = 12;
			}
			// Required
			if(!array_key_exists('required', $field)) {
				$fields[$key]['required'] = false;
			}
			// Signature
			if(!array_key_exists('signature', $field)) {
				$fields[$key]['signature'] = null;
			}
			// Disable label
			if(
				array_key_exists('wysiwyg', $field) ||
				$fields[$key]['type'] == 'files' ||
				$fields[$key]['type'] == 'include'
			) {
				$fields[$key]['line'] = true;
			}
			// Files mimes
			if($field['type'] == 'files' && !array_key_exists('mimes', $field)) {
				$regex = '/(?:.*)mimetypes:([^\|]+)(?:.*)/';
				$mimes = preg_replace($regex, '$1', $this->rules[$key]);
				$fields[$key]['mimes'] = $mimes;
			}
			$fields[$key] = (object) $fields[$key];
		}
		$this->form = $fields;

		return $this->form;
	}

   /**
    * @param object $item
    * @param string $field
    * @param $formats - for video
    * @return array
    */
	protected function getFiles (object $item, string $field, $formats = [], $customMimeType = null): array //$item, 'poster_url', '[240p]'
	{
		$files = [];
		$value = $item[$field];
		if(is_string($value)) {
			$files = [$value];
		}
		foreach ($files as $key => $file) {
			if(!File::exists($file)) {
			   if (count($formats) > 0) {
               if (!File::exists(str_replace('.', '_'.$formats[0].'.', $file))) {
                  unset($files[$key]);
                  continue;
               }
            } else {
               unset($files[$key]);
               continue;
            }
			}
			$files[$key] = (object) [
				'src'  => $file,
				'formats'  => $formats,
				'mime' => $customMimeType ?? mime_content_type(count($formats) > 0 ? str_replace('.', '_'.$formats[0].'.', $file) : $file)
			];
		}
		return $files;
	}

    abstract protected function init ();

    public function index (Request $request)
    {
        $this->init();

        $items = $this->model::orderBy('id')
            ->paginate(20);

        $data = [
            'page'  => $this->getPage(),
            'items' => $items,
        ];
        return view('admin._list', $data);
    }


    public function store (Request $request)
    {
        $this->init();
        if($this->checkStore()) {
            $request->validate($this->rules);
            $item = $this->model::create($request->all());

            if($request->ajax()) {
                return response()->json($item);
            }

            return redirect()
                ->route("{$this->page['route']}.index")
                ->with('alert', "Stored. ID: <b>{$item->id}</b>");
        }

        return $this->errorDrop($request);
    }

    protected function preUpdate ($id, Request $request) {
    }


    public function update (int $id, Request $request)
    {
        $this->init();
        $item = $this->model::findOrFail($id);
        if($this->checkUpdate($item)) {
            $this->preUpdate($id, $request);
            $request->validate($this->rules);

            $item->fill($request->all());
            $item->save();

            if($request->ajax()) {
                return response()->json($item);
            }

            return redirect()
                ->route("{$this->page['route']}.index")
                ->with('alert', "Edited. ID: <b>{$item->id}</b>");
        }

        return $this->errorDrop($request);
    }

    public function destroy (int $id, Request $request)
    {
        $this->init();
        return $this->delete($id, $request);
    }

    public function delete (int $id, Request $request)
	{
		$item = $this->model::findOrFail($id);
		if($this->checkDrop($item)) {
			$item->delete();

			if($request->ajax()) {
				$response = 'Removed. Item id: <b>'. $item->id .'</b>';
				return response()->json($response);
			}

			return redirect()
			->route("{$this->page['route']}.index")
			->with('alert', "Removed. Item id: <b>{$item->id}</b>");
		}

		return $this->errorDrop($request);
	}

	private function createMenu (): void
	{
		$links = config('admin._menu');

		$links['users']['0']['statistic'] = [
			'title' => 'Statistics',
			'ico' => 'chart-bar fa'
		];
		
		$fills = $this->getFillMenuValues();

		$links = array_merge_recursive($links, $fills);

		if(Gate::denies('ADMIN_VIEW_MAIN')) {
			$links = [];
		}

		foreach ($links as $key => $link) {
			$link['route'] = "admin.{$key}.index";
			if(!Route::has($link['route'])) {
				continue;
			}
			if(array_key_exists(0, $link)) {
				foreach($link[0] as $key2 => $link2) {
					$link2 = (object) $link2;
					if (strpos($key2, '.'))
						$link2->route = "admin.{$key2}";
					else
						$link2->route = "admin.{$key2}.index";
					// if(!Route::has($link2->route)) {
					// 	continue;
					// }
					$link['drop'][$key2] = $link2;
				}
				unset($link[0]);
			}
			$link = (object) $link;


			$this->menu[$key] = $link;
		}
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

	private function getFillMenuValues(): array
    {
        $fills = [];
        $newMessagesCount = 0;
        $lessonRooms = Room::where('subject_type', 'App\\Lesson')->get();
        foreach ($lessonRooms as $lessonRoom) {
            $newMessagesCount += count($lessonRoom->newMessages()->get());
        }
        if ($newMessagesCount > 0) {
            $fills['chat'] = ['badge' => "<span id='menu-chat-new' data-count='{$newMessagesCount}' class='badge badge-danger badge-pill'>{$newMessagesCount}</span>"];
        }
        return $fills;
    }
}
