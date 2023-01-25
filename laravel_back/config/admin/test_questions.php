<?php

return [
	'form' => [
		'test_id' => [
			'type'      => 'select',
			'signature' => 'Test',
			'items' => [],
		],
		'type' => [
            'type'      => 'select',
            'items'		=> [],
            'signature' => 'Type',
            'class'     => 12,
        ],
        'title' => [
			'type'      => 'wysiwyg',
			'signature' => 'Titile',
			'media'     => 'media/wisiwyg/test_questions/'
		],
		'number_of_options' => [
			'type'      => 'include',
			'signature' => 'Number of Options',
			'class'  => 12,
			'required'  => true,
		],
		
		'mark' => [
			'type'      => 'number',
			'signature' => 'Total Mark',
			'class'  => 12,
			'required'  => true,
		],
	],
	'rules' => [
		'type'  => "required",
		'title'  => "required",
		'mark' => 'required'
	],
];
