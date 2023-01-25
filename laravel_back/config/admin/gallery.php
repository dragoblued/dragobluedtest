<?php

return [
   'form' => [
      'type' => [
         'type'      => 'include',
         'class'     => 12
      ],
      'name' => [
         'type'      => 'text',
         'signature' => 'Name',
         'class'     => 12
      ],
      'url' => [
         'type'      => 'files',
         'signature' => 'File',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'gallery/', // public/media/lessons
         'names'     => 'id', // types(id, route)
         'prefix'      => null, // _poster
         'formatsField' => 'available_formats',
         'sizers'    =>  [
            'dynamic' => // percentage of uploaded photo
               [
                  '_min' => [ 0.5, 90 ],
                  '_preload' => [ 0.1, 50 ],
               ]
         ]
      ],
      'should_replace_poster' => [
         'type'      => 'include',
         'class'     => 12
      ],
      'poster_url' => [
         'type'      => 'files',
         'signature' => 'Poster file',
         'multiple'  => false,
         'dir' => 'public/',
         'folder'    => 'media/',
         'path'    => 'gallery/', // public/media/lessons
         'names'     => 'id', // types(id, route)
         'prefix'      => '_poster', // _poster
         'sizers'    =>  [
            'dynamic' => // percentage of uploaded photo
               [
                  '_min' => [ 0.5, 90 ],
                  '_preload' => [ 0.1, 50 ],
               ]
         ]
      ]
   ],

   'rules' => [
      'url' => 'mimetypes:image/*',
      'poster_url' => 'mimetypes:image/*',
   ]
];
