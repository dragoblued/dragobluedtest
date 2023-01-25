<?php

return [
	'form' => [
		'title' => [
			'type'      => 'text',
			'signature' => 'Title',
			'class'  => 12,
			'required'  => true,
		],
		'course_id' => [
            'type'      => 'select',
            'items'		=> [],
            'signature' => 'Course',
            'class'     => 12,
        ],
        'duration' => [
			'type'      => 'number',
			'signature' => 'Duration',
			'class'  => 12,
			'required'  => true,
		],
		'minimum_percentage' => [
			'type'      => 'number',
			'signature' => 'Minimum Percentage',
			'class'  => 12,
			'required'  => true,
		],
		'instruction' => [
            'type'      => 'wysiwyg',
            'signature' => 'Instruction',
            'media'     => 'media/wisiwyg/tests/'
        ],
		'permitted_attempt_number' => [
			'type'      => 'number',
			'signature' => 'Permitted attempt number',
			'class'  => 12,
			'required'  => true,
		],
		'total_mark' => [
			'type'      => 'number',
			'signature' => 'Total Mark',
			'class'  => 12,
			'required'  => true,
		],
		'status' => [
            'type'      => 'select',
            'items'		=> [],
            'signature' => 'Course',
            'class'     => 12,
        ],
	],
	'rules' => [
		'title'  => "required",
		'course_id'  => "required|unique:tests",
		'duration' => 'required|numeric',
		'instruction' => 'nullable',
		'minimum_percentage' => 'numeric|min:0|max:100',
		'permitted_attempt_number' => 'required'
	],
];
