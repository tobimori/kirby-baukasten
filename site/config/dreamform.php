<?php

return [
	'multiStep' => false,
	'mode' => 'htmx',
	'useDataAttributes' => true,
	'precognition' => true,
	'guards' => [
		'available' => ['honeypot']
	],
	'actions' => [
		'email' => [
			'from' => [
				'email' => env('KIRBY_MAIL_FROM')
			]
		]
	]
];
