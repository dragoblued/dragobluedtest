<?php

return [
   'form' => [
      'theme' => [
         'type'      => 'wysiwyg',
         'signature' => 'Theme',
         'wysiwyg'   => true,
         'media'     => 'media/wisiwyg/pages/'
      ],
      'authors' => [
         'type'      => 'text',
         'signature' => 'Authors',
      ],
      'file_url' => [
         'type'      => 'text',
         'signature' => 'File url',
      ],
      'file_ext' => [
         'type'      => 'text',
         'signature' => 'File ext',
      ],
   ],
   'rules' => [
      'theme' => 'nullable',
      'authors' => 'nullable',
      'file_url' => 'nullable',
      'file_ext' => 'nullable',
   ]
];
