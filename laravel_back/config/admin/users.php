<?php

return [
	'form' => [
      'avatar_url' => [
         'type'      => 'files',
         'signature' => 'Avatar',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'users/', // public/media/lessons
         'names'     => 'id', // types(id, route)
         'prefix'    => '_avatar',
         'sizers'    =>  [
            'dynamic' =>
               [
                  '_min' => [ 0.5, 90 ],
                  '_preload' => [ 0.1, 50 ]
               ]
         ]
      ],
		'active' => [
			'type'      => 'select',
			'items'     => [
				'No',
				'Yes',
			],
			'signature' => 'Active',
			'required'  => true,
			'class'     => 2,
		],
		'login' => [
			'type'      => 'text',
			'signature' => 'Login',
			'class'     => 4
		],
		'email' => [
			'type'      => 'email',
			'signature' => 'E-mail',
			'class'     => 4,
			'required'  => true,
		],
		'name' => [
			'type'      => 'text',
			'signature' => 'Name',
			'class'     => 4
		],
      'surname' => [
         'type'      => 'text',
         'signature' => 'Surname',
         'class'     => 4
      ],
      'phone' => [
         'type'      => 'tel',
         'signature' => 'Phone number',
         'class'     => 3
      ],
		'role_id' => [
			'type'      => 'select',
			'items'		=> [],
			'signature' => 'Role',
			'class'     => 4,
			'required'	=> true,
		],
		'groups' => [
		   'line'      => true,
			'type'      => 'checkbox',
			'items'		=> [],
			'signature' => 'Groups',
			'class'     => 10,
		],
		'password' => [
			'type'      => 'password',
			'signature' => 'New password',
			'class'     => 4
		],
		'password_confirmation' => [
			'type'      => 'password',
			'signature' => 'Confirm password',
			'class'     => 4
		]
	],
	'rules' => [
      'avatar_url' => 'nullable|mimetypes:image/*',
		'active'   => 'required|boolean',
		'login'    => "sometimes|required|alpha_dash|between:3,191|unique:users,login",
		'email'    => "sometimes|required|email|max:191|unique:users,email",
		'password' => "sometimes|required|min:6|confirmed",
		'role_id'  => "sometimes|required",
	]
];
