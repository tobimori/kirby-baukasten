<?php

use Kirby\Data\Json;

return [
	'code' => 'en',
	'default' => false,
	'direction' => 'ltr',
	'locale' => [
		'LC_ALL' => 'en_US'
	],
	'name' => 'English',
	'translations' => Json::read(__DIR__ . '/../translations/de.json'),
	'url' => '/en'
];
