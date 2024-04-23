<?php

return [
	'multiStep' => false,
	'mode' => 'htmx',
	'secret' => env('DREAMFORM_SECRET'),
	'guards' => [
		'available' => ['honeypot']
	],
];
