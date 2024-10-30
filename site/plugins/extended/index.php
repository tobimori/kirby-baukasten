<?php

use Kirby\Cms\App;

App::plugin('project/extended', []);

// helpers
if (!function_exists('icon')) {
	function icon(string $type, array|string $class = null, array $attr = null): string
	{
		return snippet('icon', compact('type', 'class', 'attr'), true);
	}
}
