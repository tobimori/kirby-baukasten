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
	 * Resolves the SVG sprite path from the Vite manifest, with memoization.
	 */
	function spritePath(): string
	{
		static $resolved = null;
		if ($resolved !== null) {
			return $resolved;
		}

		$manifestPath = kirby()->root('index') . '/dist/manifest.json';
		if (F::isReadable($manifestPath)) {
			$manifest = Json::read($manifestPath);
			foreach ($manifest as $key => $entry) {
				if (str_contains($key, '_sprite') && str_contains($key, '.svg')) {
					return $resolved = '/dist/' . $entry['file'];
				}
			}
		}

		return $resolved = Url::path(vite()->useHotFile('.never')->asset('assets/sprite.svg'));
	}
}
