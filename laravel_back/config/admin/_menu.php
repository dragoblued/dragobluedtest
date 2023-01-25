<?php
/**
 * Admin menu links and icons
 *
 * @var array
 */

return [
     'pages' => [
     	'title'  => 'Pages',
     	'ico'    => 'align-left fas',
     	[
         'page-home' => [
            'params' => ['id' => 1],
            'title' => 'Main',
            'ico'  => 'home fas',
         ],
         'page-events' => [
            'params' => ['id' => 3],
            'title' => 'Live-courses',
            'ico'  => 'cocktail fas',
         ],
         'page-about-edit' => [
     			'title' => 'About',
     			'ico'  => 'address-card fas',
     		],
     	],
     ],
    'courses' => [
        'title' => 'Video-Courses',
        'ico'   => 'play-circle fa',
        [
            'topics' => [
                'title' => 'Topics',
                'ico'   => 'play fa',
            ],
            'lessons' => [
                'title' => 'Lessons',
                'ico'   => 'play fa',
            ],
           'video-orders' => [
              'title' => 'Orders',
              'ico'   => 'euro-sign fa',
           ]
        ]
    ],
    // 'events' => [
    //     'title' => 'Live-Courses',
    //     'ico'   => 'cocktail fa',
    //     [
    //         'dates' => [
    //             'title' => 'Dates',
    //             'ico'   => 'clock fa',
    //         ],
    //        'orders' => [
    //           'title' => 'Orders',
    //           'ico'   => 'euro-sign fa',
    //        ]
    //     ]
    // ],
    'gallery' => [
        'title' => 'Gallery',
        'ico'   => 'images fa',
    ],
   'promocodes' => [
      'title' => 'Promo-codes',
      'ico'   => 'percent fa',
   ],
    'tests' => [
        'title' => 'Tests',
        'ico'   => 'file-alt fa',
        [
            'test_questions' => [
                'title' => 'Test Questions',
                'ico'   => 'question-circle fa',
            ]
        ]
    ],
//   'streams' => [
//       'title' => 'Streams',
//       'ico'   => 'broadcast-tower fa'
//   ],
   'chat' => [
      'title' => 'Chat',
      'ico'   => 'comments fa'
   ],
//   'messages' => [
//      'title' => 'Messages',
//      'ico'   => 'envelope fa'
//   ],
    'feedback' => [
        'title' => 'Feedback',
        'ico'   => 'arrow-alt-circle-right fa'
    ],
    'users' => [
        'title' => 'Users',
        'ico'   => 'user-friends fa',
        [
            'groupes' => [
                'title' => 'Groups',
                'ico'  => 'users fa',
            ],
            'roles' => [
                'title' => 'Roles',
                'ico'  => 'user-circle fa',
            ],
            'permissions' => [
                'title' => 'Access right',
                'ico'  => 'unlock-alt fa',
            ]
        ]
    ],
    'settings' => [
        'title' => 'Parameters',
        'ico'   => 'cogs fa',
        [
//           'page_content' =>
//              [
//                 'title' => 'PDF',
//                 'ico' => 'file-alt fa',
//              ],
           'social_links' =>
              [
                 'title' => 'Social links',
                 'ico' => 'share-alt fa'
              ],
           'currency' =>
              [
                 'title' => 'Currency',
                 'ico' => 'credit-card fa'
              ],
           'address' =>
              [
                 'title' => 'Address',
                 'ico' => 'location-arrow fa'
              ]
        ]
    ]
];
