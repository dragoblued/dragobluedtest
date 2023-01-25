<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $table = 'groupes';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'value',
		'name',
		'description'
	];

	/* Связанные таблицы */

	/* Преобразование полей */

	/* Преобразование полей (save) */

	/* Заготовки запросов */
}
