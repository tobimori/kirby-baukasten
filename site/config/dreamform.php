<?php

return [
	'multiStep' => false,
	'mode' => 'htmx',
	'useDataAttributes' => true,
	'precognition' => true,
	'secret' => env('DREAMFORM_SECRET'),
	'guards' => [
		'available' => ['honeypot']
	],
];
