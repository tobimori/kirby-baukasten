<?php

use Kirby\Data\Yaml;

return [
	'code' => 'en',
	'default' => false,
	'direction' => 'ltr',
	'locale' => [
		'LC_ALL' => 'en_US'
	],
	'name' => 'English',
	'translations' => Yaml::read(__DIR__ . '/../translations/en.yml'),
	'url' => '/en'
];
