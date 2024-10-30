<?php

use Kirby\Data\Yaml;

return [
	'code' => 'de',
	'default' => true,
	'direction' => 'ltr',
	'locale' => [
		'LC_ALL' => 'de_DE'
	],
	'name' => 'Deutsch',
	'translations' => Yaml::read(__DIR__ . '/../translations/de.yml'),
	'url' => ''
];
