<?php

return [
	'form' => [
		'active' => [
			'type'      => 'select',
			'items'     => [
				'Нет',
				'Да',
			],
			'signature' => 'Active',
			'required'  => true,
		],
		'route' => [
			'type'      => 'text',
			'signature' => 'Route',
			'required'  => true,
			'class'     => 4,
		],
		'title' => [
			'type'      => 'text',
			'signature' => 'SEO: TITLE',
			'required'  => true,
			'class'     => 4,
		],
		'h1' => [
			'type'      => 'text',
			'signature' => 'SEO: H1',
			'class'     => 4,
		],
		'text' => [
			'type'      => 'textarea',
			'signature' => 'Text',
			'wysiwyg'   => true,
			'media'     => 'img/media/'
		],
		'meta_d' => [
			'type'      => 'text',
			'signature' => 'SEO: description',
			'class'     => 6,
		],
		'meta_k' => [
			'type'      => 'text',
			'signature' => 'SEO: keywords',
			'class'     => 6,
		],
	],
	'rules' => [
		'route'  => "required|alpha_dash|unique:pages,route",
		'title'  => "required|max:191",
		'h1'     => "max:191",
		'meta_d' => "max:191",
		'meta_k' => "max:191",
	],
];
