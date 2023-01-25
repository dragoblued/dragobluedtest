<?php

return [
   'mode' => env('STRIPE_MODE', 'test'),
   'test' => [
      'public'    => env('STRIPE_TEST_PUBLIC'),
      'secret'    => env('STRIPE_TEST_SECRET')
   ],
   'live' => [
      'public'    => env('STRIPE_LIVE_PUBLIC'),
      'secret'    => env('STRIPE_LIVE_SECRET')
   ]
];
