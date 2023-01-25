<?php

return [
   'form' => [
      'name' => [
         'type'      => 'text',
         'signature' => 'Name',
         'required'  => true,
         'class'     => 6,
      ],
      'route' => [
         'type'      => 'text',
         'signature' => 'Route [url]',
         'required'  => true,
         'class'     => 6
      ],
      'title' => [
         'type'      => 'text',
         'signature' => 'Title',
         'required'  => true,
         'class'     => 6
      ],
      'sub_title' => [
         'type'      => 'text',
         'signature' => 'Subtitle',
         'class'     => 12,
      ],
      'description' => [
         'type'      => 'wysiwyg',
         'signature' => 'Description',
         'media'     => 'media/wisiwyg/events/'
      ],
      'duration' => [
         'type'      => 'include',
         'signature' => 'Duration in days',
         'required' => true,
         'class' => 3
      ],
      'langs' => [
         'type'=> 'include',
         'signature' => 'Languages',
      ],
      'plan' => [
         'type'=> 'include',
         'signature' => 'Plan',
         'wysiwyg'   => true,
      ],
      'program' => [
         'type'=> 'include',
         'signature' => 'Program'
      ],
      'poster_url' => [
         'type'      => 'files',
         'signature' => 'Poster image',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'events/', // public/media/lessons
         'names'     => 'name', // types(id, route)
         'prefix'      => '_poster', // _poster
         'sizers'    =>  [
            'dynamic' => // percentage of uploaded photo
               [
                  '_min' => [ 0.5, 90 ],
                  '_preload' => [ 0.1, 50 ],
               ]
         ]
      ],
      'model_url' => [
         'type'      => 'files',
         'signature' => 'Model image',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'events/', // public/media/lessons
         'names'     => 'name', // types(id, route)
         'prefix'      => '_model', // _poster
          'sizers'    =>  [
              'dynamic' => // percentage of uploaded photo
                  [
                      '_min' => [ 0.5, 90 ],
                      '_preload' => [ 0.1, 50 ],
                  ]
          ]
      ],
      'collage_url' => [
         'type'      => 'files',
         'signature' => 'Collage image',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'events/', // public/media/lessons
         'names'     => 'name', // types(id, route)
         'prefix'      => '_collage', // _poster
         'sizers'    =>  [
            'dynamic' => // percentage of uploaded photo
               [
                  '_min' => [ 0.5, 90 ],
                  '_preload' => [ 0.1, 50 ],
               ]
         ]
      ],
//      'promo_video_url' => [
//         'type'      => 'files',
//         'signature' => 'Promo video',
//         'names'     => 'name',
//         'multiple'  => false,
//         'class'     => 12
//      ],
      'address' => [
         'type'=> 'include'
      ],
//        'address_howtoreach' => [
//            'type'      => 'wysiwyg',
//            'signature' => 'Address How to reach',
//            'media'     => 'media/wisiwyg/events/'
//        ],
      'actual_price' => [
         'type'=> 'include',
         'class'     => 12
      ],
      'status' => [
         'type'      => 'select',
         'items'     => [
            'editing' => 'editing',
            'published' => 'published'
         ],
         'signature' => '<span title="Editing - course doesn\'t show on site | Published - course shows on site">Status*</span>',
         'class'     => 3
      ],
      'gallery' => [
         'type'=> 'include',
         'class'     => 12
      ],
      'is_model_visible' => [
         'type'      => 'checkbox',
         'signature' => 'Should show jaw model for this module?',
         'class'     => 12
      ]
   ],
   'rules' => [
      'route' => 'nullable|unique:events',
      'name' => 'sometimes|required|max:191|unique:events',
      'title' => 'nullable',
      'sub_title' => 'nullable',
      'date' => 'nullable',
      'description' => 'nullable',
      'poster_url' => 'nullable|mimetypes:image/*',
      'model_url' => 'nullable|mimetypes:image/*',
      'collage_url' => 'nullable|mimetypes:image/*',
      'promo_video_url' => 'nullable|mimetypes:video/mp4,x-msvideo',
   ]
];
