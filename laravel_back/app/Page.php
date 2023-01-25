<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
	public $table = 'pages';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'route',
		'title',
		'meta_d',
		'meta_k',
		'content'
	];

	public function setMeta ($to)
	{
		$this->title  = $to->title;
		$this->meta_d = $to->meta_d;
		$this->meta_k = $to->meta_k;
	}

	/* Связанные таблицы */
    public function gallery() {
        return $this->belongsToMany(
            Gallery::class,
            'gallery_pivot',
            'page_id',
            'gallery_id'
        );
    }

	/* Преобразование полей */
	public function getH1Attribute ($h1)
	{
		return $h1 ?: $this->title;
	}

	/* Преобразование полей (save) */

	/* Заготовки запросов */
	public function scopeSettings ($query, $route)
	{
		return $query->where('route', $route)
		->firstOrFail();
	}
}
