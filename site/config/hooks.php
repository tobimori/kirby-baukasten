<?php

use Kirby\Exception\InvalidArgumentException;

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
	}
}

return [
	'file.create:before' => function ($file, $upload) {
		validateSvgForIcons($upload->root());
	},
	'file.replace:before' => function ($file, $upload) {
		validateSvgForIcons($upload->root());
	}
];
