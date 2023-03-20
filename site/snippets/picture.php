<?php

/**
 * @var array $formats
 * @var array @widths
 * @var string|array $class
 * @var Kirby\Cms\File|Kirby\Filesystem\Asset $image
 * @var string $sizes
 * @var bool|null $lazy
 * @var bool|null $transparent
 * @var string $alt
 * */

$attr ??= [];
$defaultWidths = [400 => 400, 800 => 800, 1000 => 1000, 1200 => 1200];

if (is_a($image, 'Kirby\Cms\File') || is_a($image, 'Kirby\Filesystem\Asset')) :
  $hasLazy = $lazy ?? true;
  $isSvg = $image->extension() == 'svg'; ?>

  <picture <?= attr(['class' => isset($class) ? (is_array($class) ? implode(' ', $class) : $class) : null, 'data-caption' => $caption ?? null, ...$attr]) ?>>
    <?php if ($isSvg) : ?>
      <?= svg($image) ?>
      <?php else :
      foreach ($formats ?? ['webp', $image->extension()] as $format) :
        $srcset = [];
        foreach ($widths ?? $defaultWidths as $key => $width) :
          $srcset[$key . 'w'] = ['width' => $width, 'format' => $format];
        endforeach; ?>
        <?php if ($hasLazy) : ?>
          <source <?= attr([
                    'type' => "image/{$format}",
                    'data-srcset' => $image->srcset($srcset),
                    'data-sizes' => $sizes ?? '100vw'
                  ]) ?>>
        <?php else : ?>
          <source <?= attr([
                    'type' => "image/{$format}",
                    'srcset' => $image->srcset($srcset),
                    'sizes' => $sizes ?? '100vw'
                  ]) ?>>
      <?php endif;
      endforeach; ?>
      <img <?= attr([
              'src' => $hasLazy ? $image->bhUri() : $image->resize(median($widths ?? $defaultWidths))->url(),
              'width' => $image->width(),
              'height' => $image->height(),
              'alt' => $alt ?? is_a($image, 'Kirby\Cms\File') ? $image->alt() : null,
              'data-lazyload' => $hasLazy || null
            ]) ?>>
    <?php endif ?>
  </picture>
<?php else : ?>
  <picture <?= attr(['class' => isset($class) ? (is_array($class) ? implode(' ', $class) : $class) : null]) ?>></picture>
<?php endif ?>