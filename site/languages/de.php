<?php

use Kirby\Data\Json;

return [
	'code' => 'de',
	'default' => true,
	'direction' => 'ltr',
	'locale' => [
		'LC_ALL' => 'de_DE'
	],
	'name' => 'Deutsch',
	'translations' => Json::read(__DIR__ . '/../translations/de.json'),
	'url' => ''
];
