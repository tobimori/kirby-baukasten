<?php

use Kirby\Cms\Media;
use Kirby\Exception\InvalidArgumentException;
use Kirby\Http\Response;

/**
 * Validates SVG files to only allow performant icon/logo-like SVGs.
 * Blocks embedded bitmaps, base64 data, and text elements.
 */
if (!function_exists('validateSvgForIcons')) {
	function validateSvgForIcons(string $path): void
	{
		$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

		if ($extension !== 'svg') {
			return;
		}

		$size = filesize($path);
		$maxSize = 50 * 1024; // 50KB

		if ($size > $maxSize) {
			throw new InvalidArgumentException(
				t('SVG files must be smaller than 50KB. Please upload a PNG instead, or optimize the SVG with SVGO.')
			);
		}

		$content = file_get_contents($path);

		if (preg_match('/<image[\s>]/i', $content)) {
			throw new InvalidArgumentException(
				t('SVG files with embedded images are not allowed. Please upload a PNG instead.')
			);
		}

		if (preg_match('/data:[^;]+;base64,/i', $content)) {
			throw new InvalidArgumentException(
				t('SVG files with embedded bitmap data are not allowed. Please upload a PNG instead.')
			);
		}

		if (preg_match('/<(text|tspan|textPath)[\s>]/i', $content)) {
			throw new InvalidArgumentException(
				t('SVG files with text elements are not allowed. Please convert text to paths, or upload a PNG instead.')
			);
		}

		if (!preg_match('/viewBox\s*=/i', $content)) {
			throw new InvalidArgumentException(
				t('SVG files must have a viewBox attribute. Please add a viewBox to the SVG, or upload a PNG instead.')
			);
		}
	}
}

return [
	'file.create:before' => function ($file, $upload) {
		validateSvgForIcons($upload->root());
	},
	'file.replace:before' => function ($file, $upload) {
		validateSvgForIcons($upload->root());
	},

	/**
	 * Serves thumbnails in the best format (avif/webp) based on the browser's Accept header.
	 * Works with extension-stripped URLs from the image snippet.
	 */
	'route:after' => function (string $path, mixed $result, bool $final) {
		if ($result !== false || $final !== true || !str_starts_with($path, 'media/')) {
			return $result;
		}

		$segments = explode('/', $path);
		$filename = end($segments);

		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'], true)) {
			return $result;
		}

		$kirby = kirby();
		[$hash, $model, $assetPath] = match ($segments[1] ?? null) {
			'pages' => [
				$segments[count($segments) - 2] ?? null,
				$kirby->page(implode('/', array_slice($segments, 2, -2))),
				null
			],
			'site' => [$segments[2] ?? null, $kirby->site(), null],
			'users' => [$segments[3] ?? null, $kirby->user($segments[2] ?? ''), null],
			'assets' => [
				$segments[count($segments) - 2] ?? null,
				null,
				implode('/', array_slice($segments, 2, -2))
			],
			default => [null, null, null]
		};

		if ($hash === null || ($model === null && $assetPath === null)) {
			return $result;
		}

		$accept = $kirby->request()->header('Accept') ?? '';
		$varyHeaders = ['headers' => ['Vary' => 'Accept']];

		// build set of formats the browser accepts
		$acceptedFormats = array_filter([
			'avif' => str_contains($accept, 'image/avif'),
			'webp' => str_contains($accept, 'image/webp'),
		]);
		$preferred = array_values(array_intersect(
			$kirby->option('thumbs.formats', []),
			array_keys($acceptedFormats)
		));
		$basePath = $kirby->root('media') . '/' . implode('/', array_slice($segments, 1));

		// check if a pre-generated thumb already exists
		foreach ($preferred as $format) {
			$file = "{$basePath}.{$format}";
			if (file_exists($file)) {
				return Response::file($file, $varyHeaders);
			}
		}

		// collect thumb filenames from preferred formats and pending jobs
		$thumbFilenames = array_map(fn($f) => "{$filename}.{$f}", $preferred);
		foreach (glob(dirname($basePath) . "/.jobs/{$filename}.*.json") as $jobFile) {
			$thumbFilename = basename($jobFile, '.json');
			$format = strtolower(pathinfo($thumbFilename, PATHINFO_EXTENSION));
			if (isset($acceptedFormats[$format])) {
				$thumbFilenames[] = $thumbFilename;
			}
		}

		// try to generate and serve a thumb
		foreach (array_unique($thumbFilenames) as $thumbFilename) {
			try {
				$response = $assetPath !== null
					? Media::thumb($assetPath, $hash, $thumbFilename)
					: Media::link($model, $hash, $thumbFilename);
			} catch (\Throwable) {
				continue;
			}

			if ($response instanceof Response) {
				return $response->setHeaderFallbacks(['Vary' => 'Accept']);
			}
		}

		// fallback: serve any existing file with an acceptable format
		foreach (glob($basePath . '.*') as $file) {
			$format = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			if (isset($acceptedFormats[$format])) {
				return Response::file($file, $varyHeaders);
			}
		}

		return $result;
	}
];
