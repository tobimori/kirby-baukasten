<?php

return [
	'transport' => [
		'type' => 'smtp',
		'host' => env("KIRBY_MAIL_HOST"),
		'port' => json_decode(env("KIRBY_MAIL_PORT")),
		'security' => true,
		'auth' => 'tls',
		'username' => env("KIRBY_MAIL_USER"),
		'password' => env("KIRBY_MAIL_PASS")
	]
];
