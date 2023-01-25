<?php

return [
	'form' => [
		'key' => [
			'type'      => 'text',
			'signature' => 'Key',
			'class'  => 8,
			'required'  => true,
		],
		'value' => [
			'type'      => 'text',
			'signature' => 'Value',
			'class'  => 8,
			'required'  => true,
		],
	],
	'rules' => [
		'key'  => "required|unique:settings",
		'value'  => "required",
	],
];
