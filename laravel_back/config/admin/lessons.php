<?php

return [
   'form' => [
      'title' => [
         'type'      => 'text',
         'signature' => 'Title',
         'required'  => true,
         'class'     => 6
      ],
//      'name' => [
//         'type'      => 'hidden',
//         'signature' => 'Name',
//         'required'  => true,
//         'class'     => 6,
//      ],
      'route' => [
         'type'      => 'text',
         'signature' => 'Route [url]',
         'class'     => 6
      ],
      'topic_id' => [
         'type'      => 'select',
         'items'		=> [],
         'signature' => 'Topic',
         'class'     => 12,
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
      'poster_url' => [
         'type'      => 'files',
         'signature' => 'Poster url',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'lessons/', // public/media/lessons
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
      'should_replace_poster' => [
         'type'      => 'include',
         'class'     => 12
      ],
      'video_url' => [
         'type'      => 'files',
         'signature' => 'Main video url for upload',
         'names'     => 'name', // types(id, route)
         'prefix'      => null, // null
         'formatsField' => 'video_available_formats',
         'multiple'  => false
      ],
      'should_update_total_duration' => [
         'type'      => 'checkbox',
         'signature' => '<span title="it is available to change total duration in topic and course cards manually">While upload new main video file check this box for auto-recalculating total duration of related topic and course*</span>',
         'class' => 12
      ],
      'order' => [
         'type'      => 'order_eurodentist',
         'signature' => 'Order',
         'class'     => 3
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
      'order'   => "integer",
      'route' => 'required|max:191|unique:lessons,route',
      'name' => 'sometimes|required|max:191|unique:lessons,name',
      'title' => 'required|max:191',
      'sub_title' => 'nullable',
      'description' => 'nullable',
      'poster_url' => 'nullable|mimetypes:image/*',
      'video_url' => 'nullable|mimetypes:video/mp4,x-msvideo'
   ]
];
