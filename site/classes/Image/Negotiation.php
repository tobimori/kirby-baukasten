<?php

namespace App\Image;

use Kirby\Cms\App;
use Kirby\Cms\Media;
use Kirby\Data\Data;
use Kirby\Filesystem\F;
use Kirby\Http\Response;
use Throwable;

class Negotiation
{
	/**
	 * Serves a thumb in the best format the browser accepts (see `Accept` header)
	 * for the extension-less URLs of the image snippet
	 */
	public static function response(string $path): Response|null
	{
		$segments = explode('/', $path);
		$filename = end($segments);

		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg'], true) || strpbrk($filename, '*?[') !== false) {
			return null;
		}

		$kirby = App::instance();
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
			return null;
		}

		$dir = $kirby->root('media') . '/' . implode('/', array_slice($segments, 1, -1));

		if (!str_starts_with((string) realpath($dir), realpath($kirby->root('media')) . '/')) {
			return null;
		}

		$accept = $kirby->request()->header('Accept') ?? '';
		$headers = ['Vary' => 'Accept', 'Cache-Control' => 'public, max-age=31536000, immutable'];
		$negotiated = $kirby->option('thumbs.formats', []);
		$formats = array_values(array_filter($negotiated, fn($format) => str_contains($accept, "image/{$format}")));

		// the snippet only creates the job of the first format: copy it for all other formats,
		// as kirby deletes each job once its thumb exists
		if ($jobs = glob("{$dir}/.jobs/{$filename}.*.json")) {
			$job = Data::read($jobs[0]);
			$formats[] = $original = str_replace('jpeg', 'jpg', strtolower(F::extension($job['filename'])));

			foreach (array_unique([...$negotiated, $original]) as $format) {
				if (!file_exists("{$dir}/{$filename}.{$format}") && !file_exists($jobFile = "{$dir}/.jobs/{$filename}.{$format}.json")) {
					Data::write($jobFile, [...$job, 'format' => $format]);
				}
			}
		}

		foreach (array_unique($formats) as $format) {
			if (file_exists($file = "{$dir}/{$filename}.{$format}")) {
				return Response::file($file, ['headers' => $headers]);
			}

			try {
				$response = $assetPath !== null
					? Media::thumb($assetPath, $hash, "{$filename}.{$format}")
					: Media::link($model, $hash, "{$filename}.{$format}");
			} catch (Throwable) {
				continue;
			}

			if ($response instanceof Response) {
				return $response->setHeaderFallbacks($headers);
			}
		}

		// without jobs, the original format is unknown: serve any file that isn't a format the browser rejects
		foreach (glob("{$dir}/{$filename}.*") as $file) {
			$format = F::extension($file);
			if (
				F::name($file) === $filename &&
				in_array($format, ['jpg', 'png', 'gif', 'webp', 'avif'], true) &&
				(in_array($format, $formats, true) || !in_array($format, $negotiated, true))
			) {
				return Response::file($file, ['headers' => $headers]);
			}
		}

		return null;
	}
}
