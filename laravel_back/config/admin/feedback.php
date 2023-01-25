<?php

return [
	'form' => [
		'name' => [
			'type'      => 'text',
			'signature' => 'Name',
			'class'     => 4,
		],
		'phone' => [
			'type'      => 'text',
			'signature' => 'Number',
			'class'     => 4,
		],
		'email' => [
			'type'      => 'text',
			'signature' => 'E-mail',
			'class'     => 4,
		],
		'text' => [
			'type'      => 'textarea',
			'signature' => 'Text',
		],
	],
	'rules' => [
		'name'  => 'nullable|between:3,191',
		'phone' => [
			"required",
			"regex:/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/i",
		],
		'email' => 'nullable|email',
	],
];
