<?php

return [
	'form' => [
		'name' => [
			'type'      => 'text',
			'signature' => 'Group name',
			'class'  => 4,
			'required'  => true,
		],
		'description' => [
			'type'      => 'textarea',
			'signature' => 'Description',
			'class'  => 12,
		],
	],
	'rules' => [
		'name'  => "required|unique:groupes",
	],
];
