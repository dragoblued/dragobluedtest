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
			'class'     => 2,
		],
		'name' => [
			'type'      => 'text',
			'signature' => 'Name',
			'class'     => 4,
			'required'  => true,
		],
		'value' => [
			'type'      => 'text',
			'signature' => 'Contact',
			'class'     => 6,
			'required'  => true,
		],
		'label' => [
			'type'      => 'text',
			'signature' => 'Contact Name',
			'class'     => 5,
		],
		'group' => [
			'type'      => 'checkbox',
			'items'		=> [],
			'signature' => 'Groups',
			'class'     => 12,
		],
	],
	'rules' => [
		'active' => 'boolean',
		'name'   => "required|max:191",
		'label'  => 'max:191',
		'value'  => 'required|max:191',
		'group'  => 'max:191',
	],
];
