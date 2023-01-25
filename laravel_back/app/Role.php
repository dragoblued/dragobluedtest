<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Permission;
use App\User;

class Role extends Model
{
    public $table = 'roles';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable = [
		'name'
	];


	/* Связанные таблицы */
	public function users ()
	{
		return $this->hasMany(
			User::class,
			'role'
		);
	}

	public function permissions ()
	{
		return $this->belongsToMany(
			Permission::class,
			'roles_permissions',
			'role_id',
			'permission_id'
		);
	}
}
