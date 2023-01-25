<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $table = 'settings';

    /**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */

	protected $fillable = [
		'key',
		'value'
	];
}
