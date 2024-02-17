<?php

/**
 * @var Kirby\Cms\File|Kirby\Filesystem\Asset $image The image to display
 * @var string|null $alt An optional alt text, defaults to the file's alt text
 * @var int|null $ratio Specify a ratio for the image crop
 *
 * @var array|null $formats Which formats to render (avif, jpeg, etc.)
 * @var string|null $preset Which srcset preset to use
 * @var string|null $sizes The sizes attribute for the <img> element
 *
 * @var string|array|null $class Additional classes for the <picture> element
 * @var string|array|null $imgClass Additional classes for the <img> element
 * @var array|null $attr Additional attributes for the <picture> element
 *
 * @var bool|null $clientBlur Whether to use client-side blurhash/thumbhash, defaults to true
 * @var bool|null $lazy Whether to use lazy loading, defaults to true
 * */

$ratio ??= null;
$preset ??= 'default';
$clientBlur ??= true;
$attr ??= [];
$formats ??= ['webp', $image?->extension()];
$lazy ??= true;

if (is_a($image, 'Kirby\Cms\File') || is_a($image, 'Kirby\Filesystem\Asset')) : ?>

	<picture <?= attr([
							'class' => ['block', $class ?? ''],
							'style' => '--ratio: ' . ($ratio ?? $image->ratio()) . ';',
							...$attr
						]) ?>>

		<?php if ($image->extension() == 'svg') : ?>
			<?= svg($image) ?>
		<?php else : ?>

			<?php foreach ($formats as $format) :
				$widths = option("thumbs.srcsets.{$preset}");
				$srcset = [];
				$median = $ratio ? $image->crop(median($widths), floor(median($widths) / $ratio)) : $image->resize(median($widths)); ?>

				<?php
				// Generate srcset array
				foreach ($widths as $width) {
					$srcset[$width . 'w'] = [
						'width' => $width,
						'height' => $ratio ? floor($width / $ratio) : null,
						'crop' => $ratio ? true : false,
						'format' => $format
					];
				} ?>

				<?php if ($lazy) : ?>
					<source <?= attr([
										'type' => "image/{$format}",
										'data-srcset' => $image->srcset($srcset),
										'data-sizes' => $sizes ?? 'auto',
									]) ?>>
				<?php else : ?>
					<source <?= attr([
										'type' => "image/{$format}",
										'srcset' => $image->srcset($srcset),
										'sizes' => $sizes ?? '100vw'
									]) ?>>
				<?php endif; ?>
			<?php endforeach; ?>

			<img <?= attr([
							'data-thumbhash' => $clientBlur ? $image->th() : null,
							'src' => !$clientBlur ? $image->thUri() : null,
							'data-src' => $median->url(),
							'width' => $image->width(),
							'height' => $ratio ? floor($image->width() / $ratio) : $image->height(),
							'alt' => $alt ?? is_a($image, 'Kirby\Cms\File') ? $image->alt() : null,
							'loading' => $lazy ? "lazy" : null,
							'data-sizes' => $sizes ?? 'auto',
							'class' => cls(['w-full h-full object-cover', $imgClass ?? ' ']),
							'style' => 'aspect-ratio: ' . ($ratio ?? $image->ratio()) . '; object-position: '  . ($image->focus()->isNotEmpty() ? $image->focus() : '50% 50%'),
						]) ?>>

		<?php endif ?>
	</picture>

<?php
// Dummy element that will be rendered when specified image is not an image
else : ?>
	<picture <?= attr(['class' => ['block', $class ?? ''], ...$attr]) ?>></picture>
<?php endif ?>
