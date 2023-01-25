<?php

return [
	'form' => [
		'name' => [
			'type'      => 'text',
			'signature' => 'Name',
			'class'  => 8,
			'required'  => true,
		],
		'url' => [
			'type'      => 'text',
			'signature' => 'Url',
			'class'  => 8,
			'required'  => true,
		],

		'icon' => [
			'type'      => 'include',
			'signature' => 'Icon',
			'class'  => 8,
			'required'  => true,
		],
	],
	'rules' => [
		'name'  => "required",
		'url'  => "required",
		'icon'  => "required",
	],
];
