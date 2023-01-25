<?php

return [
   'form' => [
      'course_id' => [
         'type'      => 'select',
         'items'		=> [],
         'signature' => 'Course',
         'required'  => true,
         'class'     => 12,
      ],
//      'name' => [
//         'type'      => 'hidden',
//         'signature' => 'Name',
//         'class'     => 6,
//      ],
      'title' => [
         'type'      => 'text',
         'signature' => 'Title',
         'required'  => true,
         'class'     => 6
      ],
      'route' => [
         'type'      => 'text',
         'signature' => 'Route [url]',
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
         'wysiwyg'   => true,
         'media'     => 'media/wisiwyg/topics/'
      ],
      'poster_url' => [
         'type'      => 'files',
         'signature' => 'Poster url',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'topics/', // public/media/topics
         'names'     => 'name', // types(id, route)
         'prefix'      => '_poster', // _poster
         'sizers'    =>  [
            'dynamic' =>
               [
                  '_min' => [ 0.5, 90 ],
                  '_preload' => [ 0.1, 50 ],
               ]
         ],
      ],
//      'promo_video_url' => [
//         'type'      => 'files',
//         'signature' => 'Promo video url',
//         'names'     => 'name', // types(id, route)
//         'prefix'     => '_promo',
//      'formatsField' => 'promo_video_available_formats',
//         'multiple'  => false,
//         'class'     => 12,
//      ],
      'lessons_total_duration' => [
         'type'      => 'include',
         'signature' => 'Total lessons duration',
         'class'     => 12
      ],
      'actual_price' => [
         'type'      => 'text',
         'signature' => 'Actual price',
         'class'     => 4,
      ],
      'discount_price' => [
         'type'      => 'text',
         'signature' => 'Discount price',
         'class'     => 4,
      ],
      'order' => [
         'type'      => 'order_eurodentist',
         'signature' => 'Order',
         'class'     => 3,
      ]
//      'status' => [
//         'type'      => 'select',
//         'items'     => [
//            'editing' => 'editing',
//            'published' => 'published',
//            'coming-soon' => 'coming-soon'
//         ],
//         'signature' => '<span title="Editing, Coming-soon - topic doesnt show on site | Published - topic shows on site">Status?</span>',
//         'class'     => 3,
//      ]
   ],
   'rules' => [
      'order'   => "integer",
      'route' => 'required|max:191|unique:topics,route',
      'name' => 'sometimes|required|max:191|unique:topics,name',
      'title' => 'required|max:191',
      'sub_title' => 'nullable',
      'description' => 'nullable',

      'poster_url' => 'nullable|mimetypes:image/*',

      'promo_video_url' => 'nullable|mimetypes:video/mp4,x-msvideo',

      'is_free' => 'nullable',
      'actual_price' => 'nullable',
      'discount_price' => 'nullable',
   ]
];
