<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Role;
use App\Permission;

class RoleController extends AdminController
{
    protected $model;

	public function __construct ()
	{
		parent::__construct();
	}

	public function init () {
		$this->setPage([
			'route' => 'admin.roles',
			'title' => 'Roles - [ ADMIN ]',
			'h1'    => 'Roles'
		]);
		$this->setModel(Role::class);
		$this->setForm();
		$this->updateForm();
		$this->setRules();
	}

	private function updateForm (): void
	{
		$permissions = Permission::orderBy('id', 'desc')
		->pluck('name', 'id')
        ->toArray();

		$this->setForm('permissions.items', $permissions);
	}


	public function create ()
	{
		$this->init();
		$this->setCurrent('create');

		$data = [
			'page' => $this->getPage(),
			'form' => $this->getForm(),
		];
		return view('admin._form', $data);
	}

	public function store (Request $request)
	{
		$this->init();
        if($this->checkStore()) {
            $request->validate($this->rules);

            $item = $this->model::create($request->all());
            $item->permissions()->attach($request->get('permissions'));

            if($request->ajax()) {
                return response()->json($item);
            }

            return redirect()
                ->route("{$this->page['route']}.index")
                ->with('alert', "Stored. ID: <b>{$item->id}</b>");
        }

        return $this->errorDrop($request);
	}

	public function edit (int $id)
	{
		$this->init();
		$this->setCurrent('edit');

        $item = $this->model::with(['permissions'])->findOrFail($id)->toArray();
        $item['permissions'] = array_map(function ($group) {
            return $group['id'];
        }, $item['permissions']);

		$data = [
			'page' => $this->getPage(),
			'form' => $this->getForm(),
			'item' => $item,
		];
		return view('admin._form', $data);
	}

	public function update (int $id, Request $request)
	{
		$this->init();
		$this->setRule('name.unique', "roles,name,{$id}");
		$request->validate($this->rules);

		$item = $this->model::findOrFail($id);
        if($this->checkUpdate($item)) {
            $item->fill($request->all());
            $item->save();
            $item->permissions()->sync($request->get('permissions'));

            if($request->ajax()) {
                return response()->json($item);
            }

            return redirect()
                ->route("{$this->page['route']}.index")
                ->with('alert', "Edited. ID: <b>{$item->id}</b>");
        }

        return $this->errorDrop($request);
	}


    public function checkUpdate ($item): bool {
        return $item->id === 1 || $item->id === 2 ? false : true;
    }

    public function checkDrop ($item): bool {
        return $item->id === 1 || $item->id === 2 ? false : true;
    }

    public function errorDrop (Request $request)
    {
        if($request->ajax()) {
            $error = 'Removing or updating Admin and User roles are forbidden';
            return response()->json($error, 423);
        }

        return redirect()
            ->route("{$this->page['route']}.index")
            ->with('alert', "Removing or updating <b>Admin</b> and <b>User</b> roles are forbidden");
    }
}
