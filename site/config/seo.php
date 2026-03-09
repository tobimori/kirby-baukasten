<?php

return [
	'robots' => [
		'pageSettings' => false
	],
	'canonical' => [
		'base' => env("APP_URL")
	],
	'files' => [
		'parent' => 'site.find("page://images")',
		'template' => 'image'
	],
	'ai' => [
		'provider' => 'gemini',
		'providers' => [
			'gemini' => [
				'config' => [
					'apiKey' => env('GEMINI_API_KEY'),
				]
			]
		]
	],
	'socialMedia' => [
		'twitter' => false,
		'youtube' => false
	],
];
