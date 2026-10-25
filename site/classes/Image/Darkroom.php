<?php

namespace App\Image;

use Imagick;
use Kirby\Image\Darkroom\Imagick as ImagickDarkroom;

/**
 * Imagick driver that reads `quality` as a perceptual target (SSIMULACRA 2 score)
 * and converts it to the encoder quality of the output format
 */
class Darkroom extends ImagickDarkroom
{
	/**
	 * Encoder quality for each SSIMULACRA 2 score, interpolated linearly in between.
	 * Median of 87 thumbnails (400–2000 px photos, screenshots and graphics from client projects),
	 * measured with ImageMagick 7.1.2, libheif 1.23 with aom 3.15 at speed 6, libwebp 1.6 and libjpeg-turbo.
	 * Remeasure when the encoders or their settings below change.
	 */
	public const QUALITY = [
		'avif' => [40 => 28, 45 => 31, 50 => 35, 55 => 39, 60 => 43, 65 => 47, 70 => 53, 75 => 60, 80 => 70, 85 => 80, 90 => 94, 100 => 100],
		'webp' => [40 => 20, 45 => 21, 50 => 28, 55 => 36, 60 => 46, 65 => 58, 70 => 72, 75 => 79, 80 => 85, 85 => 90, 90 => 98, 100 => 100],
		'jpg' => [40 => 24, 45 => 27, 50 => 30, 55 => 36, 60 => 43, 65 => 53, 70 => 65, 75 => 75, 80 => 84, 85 => 91, 90 => 97, 100 => 100],
	];

	public static function encoderQuality(string $format, int $score): int|null
	{
		if (($table = static::QUALITY[$format] ?? null) === null) {
			return null;
		}

		$previous = null;

		foreach ($table as $tableScore => $quality) {
			if ($score <= $tableScore) {
				if ($previous === null) {
					return $quality;
				}

				[$previousScore, $previousQuality] = $previous;
				return (int)round($previousQuality + ($quality - $previousQuality) * ($score - $previousScore) / ($tableScore - $previousScore));
			}

			$previous = [$tableScore, $quality];
		}

		return $quality;
	}

	protected function defaults(): array
	{
		return parent::defaults() + ['avifSpeed' => 6, 'srgb' => true];
	}

	protected function resize(Imagick $image, array $options): Imagick
	{
		return $this->colors(parent::resize($image, $options), $options);
	}

	/**
	 * Converts CMYK to sRGB and drops sRGB and grayscale profiles, as browsers assume sRGB for untagged images.
	 * Other RGB profiles (Display P3, Adobe RGB) are converted to sRGB with the `srgb` option, else they stay,
	 * so wide-gamut colors survive. Keep the conversion with ImageMagick 6: its AVIF encoder drops all profiles.
	 */
	protected function colors(Imagick $image, array $options): Imagick
	{
		$colorspace = $image->getImageColorspace();

		if ($image->getImageProfiles('icc', false) === []) {
			if ($colorspace === Imagick::COLORSPACE_CMYK) {
				$image->transformImageColorspace(Imagick::COLORSPACE_SRGB);
			}

			return $image;
		}

		$isSrgb = $colorspace === Imagick::COLORSPACE_GRAY || (
			$colorspace !== Imagick::COLORSPACE_CMYK &&
			str_contains((string)$image->getImageProperty('icc:description'), 'sRGB')
		);

		if (!$isSrgb) {
			if ($colorspace !== Imagick::COLORSPACE_CMYK && $options['srgb'] !== true) {
				return $image;
			}

			$image->profileImage('icc', file_get_contents(__DIR__ . '/sRGB-v4.icc'));
		}

		$image->removeImageProfile('icc');
		return $image;
	}

	protected function save(Imagick $image, string $file, array $options): bool
	{
		$format = strtolower($options['format'] ?? pathinfo($file, PATHINFO_EXTENSION));
		$format = $format === 'jpeg' ? 'jpg' : $format;
		$image->setImageFormat($format);

		if ($format === 'avif') {
			$image->setOption('heic:speed', (string)$options['avifSpeed']);
		}

		if ($format === 'webp') {
			$image->setOption('webp:method', '6');
			$image->setOption('webp:use-sharp-yuv', 'true');
		}

		if ($format === 'jpg') {
			// imagemagick switches to 4:4:4 from quality 90, which would break the calibration above
			$image->setOption('jpeg:sampling-factor', '4:2:0');
			$image->setInterlaceScheme(Imagick::INTERLACE_PLANE);
		}

		// png: zlib level 7 with adaptive filtering, 1 % larger than level 9 at a third of the CPU time
		if ($quality = $format === 'png' ? 75 : static::encoderQuality($format, $options['quality'])) {
			// the heic coder reads the quality from the wand, the others from the image
			$image->setCompressionQuality($quality);
			$image->setImageCompressionQuality($quality);
		}

		return $image->writeImages("{$format}:{$file}", true);
	}
}
