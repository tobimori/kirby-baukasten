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
	'socialMedia' => [
		'twitter' => false,
		'youtube' => false
	],
];
