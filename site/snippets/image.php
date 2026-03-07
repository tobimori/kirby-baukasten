<?php

/**
 * @var Kirby\Cms\File|Kirby\Filesystem\Asset $image
 * @var string|null $alt
 * @var float|null $ratio
 * @var string|null $preset
 * @var string|null $sizes
 * @var string|array|null $class
 * @var array|null $attr
 * @var bool|null $clientBlur
 * @var bool|null $lazy
 */

use Kirby\Cms\File;
use Kirby\Filesystem\Asset;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Html;

$isFile = $image instanceof File;
$focus ??= $isFile ? ($image->focus()->value() ?? 'center') : 'center';
$ratio ??= null;
$preset ??= 'default';
$clientBlur ??= true;
$attr ??= [];
$attrStyle = $attr['style'] ?? '';
unset($attr['style']);
$lazy ??= true;

if ($isFile || $image instanceof Asset) :
	if ($image->extension() === 'svg') :
		$svgString = svg($image);
		if ($svgString && (($class ?? '') || $attrStyle || $attr)) {
			$doc = Dom\HTMLDocument::createFromString($svgString, LIBXML_NOERROR);
			$svg = $doc->getElementsByTagName('svg')->item(0);
			if ($svg) {
				if ($class ?? '') {
					$svg->setAttribute('class', cls([$svg->getAttribute('class'), $class]));
				}
				if ($attrStyle) {
					$existing = $svg->getAttribute('style');
					$svg->setAttribute('style', $existing ? "{$existing}; {$attrStyle}" : $attrStyle);
				}
				$altText = $alt ?? ($isFile ? $image->alt()->value() : null);
				if ($altText) {
					$svg->setAttribute('aria-label', $altText);
					$svg->setAttribute('role', 'img');
				}
				foreach ($attr as $key => $value) {
					$svg->setAttribute($key, $value);
				}
				$svgString = $doc->saveHtml($svg);
			}
		} ?>
		<?= $svgString ?>
	<?php else :
		$widths = option("thumbs.srcsets.{$preset}");
		$formats = array_unique([...option('thumbs.formats', []), $image->extension()]);
		$srcsetString = null;

		// generates srcsets for all formats — only the first string is kept,
		// but all iterations trigger thumb job creation for content negotiation
		foreach ($formats as $format) {
			$srcsetConfig = [];
			foreach ($widths as $width) {
				$srcsetConfig["{$width}w"] = [
					'width' => $width,
					'height' => $ratio ? floor($width / $ratio) : null,
					'crop' => (bool) $ratio,
					'format' => $format
				];
			}

			$generated = $image->srcset($srcsetConfig);
			$srcsetString ??= $generated;
		}

		$midWidth = $widths[intdiv(count($widths) - 1, 2)];
		$midThumb = $image->thumb([
			'width' => $midWidth,
			'height' => $ratio ? floor($midWidth / $ratio) : null,
			'crop' => (bool) $ratio,
			'format' => $formats[0]
		]);

		$srcsetString = preg_replace('/\.[a-z0-9]+(\s+\d+w)/', '$1', $srcsetString);
		$medianUrl = preg_replace('/\.[a-z0-9]+$/', '', $midThumb->url());
		$src = $clientBlur
			? 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs='
			: $image->thUri($ratio);
	?>
		<?= Html::img($src, [
			'data-thumbhash' => $clientBlur ? $image->th($ratio) : null,
			'data-src' => $medianUrl,
			'width' => $image->width(),
			'height' => $ratio ? floor($image->width() / $ratio) : $image->height(),
			'alt' => $alt ?? ($isFile ? $image->alt() : null),
			'loading' => $lazy ? 'lazy' : null,
			'draggable' => false,
			($lazy ? 'data-srcset' : 'srcset') => $srcsetString,
			($lazy ? 'data-sizes' : 'sizes') => $sizes ?? ($lazy ? 'auto' : '100vw'),
			'class' => cls(['object-cover pointer-events-none', $class ?? '']),
			'style' => A::join(array_filter([
				!$image->hasTransparency($ratio) ? "background-color: " . $image->averageColor(ratio: $ratio) : null,
				"aspect-ratio: " . ($ratio ?? $image->ratio()),
				"object-position: " . ($focus ?? "50% 50%"),
				$attrStyle ?: null,
			]), '; '),
			...$attr,
		]) ?>
	<?php endif ?>
<?php endif ?>
