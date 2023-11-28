<?php

use Kirby\Data\Yaml;
use Kirby\Filesystem\F;

return [
	'code' => 'en',
	'default' => false,
	'direction' => 'ltr',
	'locale' => [
		'LC_ALL' => 'en_US'
	],
	'name' => 'English',
	'translations' => Yaml::decode(F::read(dirname(__DIR__) . '/translations/en.yml')),
	'url' => '/en/'
];
