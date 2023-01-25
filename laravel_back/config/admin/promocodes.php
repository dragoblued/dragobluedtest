<?php

return [
   'form' => [
      'code' => [
         'type'      => 'include',
         'signature' => 'Code',
         'class'     => 12,
         'required'  => true,
      ],
      'discount_type' => [
         'type'      => 'select',
         'signature' => 'Discount type',
         'items'     => [
            'percent' => 'Percents',
            'numeric' => 'Numeric'
         ],
         'class'     => 4,
         'required'  => true
      ],
      'discount' => [
         'type'      => 'number',
         'signature' => 'Discount value',
         'default'   => 0,
         'min'       => 1,
         'class'     => 3,
         'required'  => true
      ],
      'daterange' => [
         'type'      => 'include',
         'signature' => 'Availability date range',
         'class'     => 5,
         'required'  => true
      ],
      'usage_limit' => [
         'type'      => 'number',
         'signature' => 'Limit count',
         'default'   => 1,
         'min'       => 1,
         'class'     => 3,
         'required'  => true
      ],
      'subject' => [
         'type'      => 'include',
         'signature' => 'Specify item (Leave blank if you want promocode for any items)',
         'required'  => false,
         'class'     => 12
      ]

   ]
];
