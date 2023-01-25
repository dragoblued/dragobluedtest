<?php

return [
	'form' => [
		'name' => [
			'type'      => 'text',
			'signature' => 'Role',
			'class'  => 4,
			'required'  => true,
		],
		'permissions' => [
			'line'      => 'true',
			'type'      => 'checkbox',
			'items'		=> [],
			'signature' => 'Access rights',
			'class'  => 12,
			'required'  => true,
		],
	],
	'rules' => [
		'name'  => "required|unique:roles",
		'permissions'  => "required",
	],
];
