<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Group;

class Contact extends Model
{
	public $table = 'contacts';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $casts = [
        'sector' => 'array',
        'group' => 'array'
    ];

	protected $fillable = [
		'active',
		'name',
		'sector',
		'phone',
		'email',
		'group',
	];

	/* Связанные таблицы */
	public function groupes ()
	{
		return $this->belongsToMany(
			Group::class,
			'contacts_groupes',
			'contact_id',
			'group_id'
		);
		// return $this->belongsToMany('App\Group', 'contacts_groupes');
	}

	/* Преобразование полей */
	public function getActiveTextAttribute (): string
	{
		$data = $this->active ? 'Да' : 'Нет';

		return $data;
	}

	/* Преобразование полей (save) */

	/* Заготовки запросов */
}
