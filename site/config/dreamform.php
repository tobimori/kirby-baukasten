<?php

return [
	'multiStep' => false,
	'mode' => 'htmx',
	'precognition' => true,
	'secret' => env('DREAMFORM_SECRET'),
	'guards' => [
		'available' => ['honeypot']
	],
];
