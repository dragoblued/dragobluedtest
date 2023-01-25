<?php

return [
    'form' => [
        'title' => [
            'type'      => 'text',
            'signature' => 'Title',
            'required'  => true,
            'class'     => 6
        ],
        'name' => [
            'type'      => 'text',
            'signature' => 'Name',
            'required'  => true,
            'class'     => 6,
        ],
        'sub_title' => [
            'type'      => 'text',
            'signature' => 'Subtitle',
            'class'     => 12,
        ],
        'description' => [
            'type'      => 'wysiwyg',
            'signature' => 'Description',
            'wysiwyg'   => true,
            'media'     => 'media/wisiwyg/lessons/'
        ],
        'appointment_datetime' => [
            'type'      => 'include',
            'class'     => 12
        ],
        'poster_url' => [
            'type'      => 'files',
            'signature' => 'Poster url',
            'multiple'  => false,
            'dir' => 'public/',
            'folder'    => 'media/',
            'path'    => 'streams/', // public/media/lessons
            'names'     => 'name', // types(id, route)
            'prefix'      => '_poster', // _poster
            'sizers'    =>  [
                'dynamic' =>
                    [
                        '_min' => [ 0.5, 90 ],
                        '_preload' => [ 0.1, 50 ]
                    ]
            ],
        ],
//      'should_replace_poster' => [
//         'type'      => 'include',
//         'class'     => 12
//      ],
//        'recorded_video_url' => [
//            'type'      => 'files',
//            'signature' => 'Recorded video url for upload',
//            'names'     => 'name', // types(id, route)
//            'prefix'      => null, // null
//            'formatsField' => 'video_available_formats',
//            'multiple'  => false,
//        ],
        'allowed_users' => [
            'type'      => 'include',
            'class'     => 12
        ],
        'lang' => [
            'type'      => 'select',
            'items'     => [
                'English' => 'English',
                'Lithuanian' => 'Lithuanian',
                'Russian' => 'Russian'
            ],
            'signature' => 'Language',
            'class'     => 3
        ],
        'is_free' => [
            'type'      => 'select',
            'items'     => [
                'No',
                'Yes',
            ],
            'signature' => 'Is free',
            'class'     => 3
        ]
    ],

    'rules' => [
        'name' => 'required|max:191|unique:streams,name',
        'title' => 'required|max:191',
        'sub_title' => 'nullable',
        'description' => 'nullable',
        'poster_url' => 'nullable|mimetypes:image/*'
//        'recorded_video_url' => 'nullable|mimetypes:video/mp4'
    ]
];
