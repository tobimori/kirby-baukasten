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
		'provider' => 'openrouter',
		'providers' => [
			'openrouter' => [
				'config' => [
					'apiKey' => env('OPENROUTER_API_KEY'),
					'model' => 'google/gemini-2.5-flash-preview-09-2025',
				]
			]
		]
	],
	'socialMedia' => [
		'twitter' => false,
		'youtube' => false
	],
];
