<?php

/**
 * @var Kirby\Cms\File|Kirby\Filesystem\Asset $image
 * @var string|null $alt
 * @var float|null $ratio
 * @var string|null $preset
 * @var string|null $sizes
 * @var int|null $quality SSIMULACRA 2 score, see site/config/thumbs.php
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
		if ($svgString) {
			$doc = Dom\HTMLDocument::createFromString($svgString, LIBXML_NOERROR);
			$svg = $doc->getElementsByTagName('svg')->item(0);
			if ($svg) {
				$viewBox = $svg->getAttribute('viewBox');
				if ($viewBox && preg_match('/[\d.]+\s+[\d.]+\s+([\d.]+)\s+([\d.]+)/', $viewBox, $m)) {
					$ratioStyle = "--ratio: {$m[1]} / {$m[2]}";
					$existing = $svg->getAttribute('style');
					$svg->setAttribute('style', $existing ? "{$existing}; {$ratioStyle}" : $ratioStyle);
				}
				if ($class ?? '') {
					$svg->setAttribute('class', cls([$svg->getAttribute('class'), $class]));
				}
				if ($attrStyle) {
					$existing = $svg->getAttribute('style');
					$svg->setAttribute('style', $existing ? "{$existing}; {$attrStyle}" : $attrStyle);
				}
				if (isset($alt)) {
					$svg->setAttribute('aria-label', $alt);
					$svg->setAttribute('role', 'img');
				} elseif ($isFile) {
					$altText = $image->alt()->toAltText();
					if ($altText->isDecorative()) {
						$svg->setAttribute('role', 'presentation');
					} elseif ($altText->text() !== '') {
						$svg->setAttribute('aria-label', $altText->text());
						$svg->setAttribute('role', 'img');
					}
				}
				foreach ($attr as $key => $value) {
					$svg->setAttribute($key, $value);
				}
				$svgString = $doc->saveHtml($svg);
			}
		} ?>
		<?= $svgString ?>
	<?php else :
		$presetWidths = option("thumbs.srcsets.{$preset}");
		$maxWidth = (int) ($ratio ? min($image->width(), $image->height() * $ratio) : $image->width());
		$widths = array_values(array_filter($presetWidths, fn($width) => $width < $maxWidth));

		// kirby doesn't upscale, so larger widths would be copies of the source with wrong `w` descriptors
		if (count($widths) < count($presetWidths)) {
			$widths[] = $maxWidth;
		}

		// the URLs have no extension: `App\Image\Negotiation` (site/config/hooks.php)
		// serves the best format the browser accepts and creates the jobs of the other formats
		$urls = [];
		foreach ($widths as $width) {
			$urls[$width] = preg_replace('/\.[a-z0-9]+$/', '', $image->thumb([
				'width' => $width,
				'height' => $ratio ? (int) floor($width / $ratio) : null,
				'crop' => (bool) $ratio,
				'format' => option('thumbs.formats')[0] ?? $image->extension(),
				'quality' => $quality ?? option('thumbs.quality'),
			])->url());
		}

		$src = $clientBlur
			? 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs='
			: $image->thumbhashUri($ratio);
		$altAttr = isset($alt) ? ['alt' => $alt] : ($isFile ? $image->alt()->toAltText()->toAttr() : []); ?>
		<?= Html::img($src, [
			'data-thumbhash' => $clientBlur ? $image->thumbhash($ratio) : null,
			'data-src' => $urls[$widths[intdiv(count($widths) - 1, 2)]],
			'width' => $image->width(),
			'height' => $ratio ? floor($image->width() / $ratio) : $image->height(),
			'loading' => $lazy ? 'lazy' : null,
			'draggable' => false,
			($lazy ? 'data-srcset' : 'srcset') => implode(', ', array_map(fn($width, $url) => "{$url} {$width}w", array_keys($urls), $urls)),
			($lazy ? 'data-sizes' : 'sizes') => $sizes ?? ($lazy ? 'auto' : '100vw'),
			'class' => cls(['object-cover pointer-events-none', $class ?? '']),
			'style' => A::join(array_filter([
				!$image->hasTransparency($ratio) && ($color = $image->averageColor(ratio: $ratio)) ? "background-color: {$color}" : null,
				"aspect-ratio: " . ($ratio ?? $image->ratio()),
				"object-position: " . ($focus ?? "50% 50%"),
				$attrStyle ?: null,
			]), '; '),
			...$altAttr,
			...$attr,
		]) ?>
	<?php endif ?>
<?php endif ?>
