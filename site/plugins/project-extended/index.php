<?php

use Kirby\Cms\App;
use Kirby\Data\Yaml;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;

App::plugin('project/extended', [
	/**
	 * Automatically load translations from `site/translations` folder
	 */
	'translations' => A::keyBy(A::map(
		Dir::read($dir = dirname(__DIR__, 2) . '/translations'),
		fn ($file) => A::merge(['lang' => F::name($file)], Yaml::decode(F::read($dir . '/' . $file)))
	), 'lang')
]);

// helpers
if (!function_exists('icon')) {
	function icon(string $type, array|string $class = null, array $attr = null): string
	{
		return snippet('icon', compact('type', 'class', 'attr'), true);
	}
}
