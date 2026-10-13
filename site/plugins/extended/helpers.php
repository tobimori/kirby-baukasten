<?php

// helpers
if (!function_exists('icon')) {
	function icon(string $type, array|string|null $class = null, array $attr = []): string
	{
		return snippet('icon', compact('type', 'class', 'attr'), true);
	}
}

if (!function_exists('spritePath')) {
	/**
	 * Resolves the path of the built SVG sprite (relative to the index root) from the Vite manifest.
	 */
	function spritePath(): string
	{
		return Url::path(LukasKleinschmidt\Vite::copy()->useHotFile('.never')->asset('spritemap.svg'));
	}
}
