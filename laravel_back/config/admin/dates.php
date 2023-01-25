<?php 

return [
	'form' => [
		'year' => [
			'type'      => 'text',
			'signature' => 'Year',
			'class'  => 6,
			'required'  => true,
		],
		'daterange' => [
            'type'      => 'include',
            'signature' => 'Date range',
            'class'  => 6,
            'required'  => true,
        ],
		'seats_total' => [
			'type'      => 'number',
			'signature' => 'Seats total',
			'class'  => 6,
			'required'  => true,
		],
		'event_id' => [
			'type'      => 'include',
			'signature' => 'Event',
			'required' => true,
		],
		/*'event' => [
			'type'      => 'include',
			'signature' => 'Language',
			'required' => true,
		],*/
		
	],
	'rules' => [
		'year' => 'required',
        'daterange' => 'required',
        'event_id' => 'required',
        'seats_total' => 'required',
        'lang' => 'required',
	],
];