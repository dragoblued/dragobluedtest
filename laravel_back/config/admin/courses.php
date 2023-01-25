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
//         'class'     => 6,
//      ],
      'route' => [
         'type'      => 'text',
         'signature' => 'Route [url]',
         'required'  => true,
         'class'     => 6
      ],

      'sub_title' => [
         'type'      => 'text',
         'signature' => 'Subtitle',
         'class'     => 12
      ],
      'description' => [
         'type'      => 'wysiwyg',
         'signature' => 'Description',
         'wysiwyg'   => true,
         'media'     => 'media/wisiwyg/courses/'
      ],
      'poster_url' => [
         'type'      => 'files',
         'signature' => 'Poster url',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'courses/', // public/media/courses
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
      'promo_video_url' => [
         'type'      => 'files',
         'signature' => 'Promo video url',
         'names'     => 'name', // types(id, route)
         'prefix'     => '_promo',
         'formatsField' => 'promo_video_available_formats',
         'multiple'  => false,
         'class'     => 12,
      ],
//      'refund_policy' => [
//         'type'      => 'wysiwyg',
//         'signature' => 'Refund policy',
//         'media'     => 'media/wisiwyg/courses/'
//      ],
//      'what_will_students_learn' => [
//         'type'      => 'wysiwyg',
//         'signature' => 'What will students learn',
//         'media'     => 'media/wisiwyg/courses/'
//      ],
//      'target_students' => [
//         'type'      => 'wysiwyg',
//         'signature' => 'Target students',
//         'media'     => 'media/wisiwyg/courses/'
//      ],
//      'requirements' => [
//         'type'      => 'wysiwyg',
//         'signature' => 'Requirements',
//         'media'     => 'media/wisiwyg/courses/'
//      ],
      'lessons_total_duration' => [
         'type'      => 'include',
         'signature' => 'Total lessons duration',
         'class'     => 12
      ],
      'actual_price' => [
         'type'      => 'text',
         'signature' => 'Actual price',
         'class'     => 4
      ],
      'discount_price' => [
         'type'      => 'text',
         'signature' => 'Discount price',
         'class'     => 4
      ],
      'order' => [
         'type'      => 'order_eurodentist',
         'signature' => 'Order',
         'class'     => 3
      ],
      'status' => [
         'type'      => 'select',
         'items'     => [
            'editing' => 'editing',
            'published' => 'published',
            'coming-soon' => 'coming-soon'
         ],
         'signature' => '<span title="Editing - course doesn\'t show on site | Published - course shows on site | Coming-soon - course shows on site with coming-soon badge">Status*</span>',
         'class'     => 3
      ]
   ],
   'rules' => [
      'order'   => "integer",
      'route' => 'required|max:191|unique:courses',
      'name' => 'sometimes|required|max:191|unique:courses',
      'title' => 'required|max:191',
      'sub_title' => 'nullable',
      'description' => 'nullable',
      'faq' => 'nullable',
      'tags' => 'nullable',

      'poster_url' => 'nullable|mimetypes:image/*',

      'promo_video_url' => 'nullable|mimetypes:video/mp4',

      'what_will_students_learn' => 'nullable',
      'target_students' => 'nullable',
      'requirements' => 'nullable',
      'actual_price' => 'nullable',
      'discount_price' => 'nullable',
   ]
];
