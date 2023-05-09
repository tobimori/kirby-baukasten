<?php

/**
 * @var array $formats
 * @var array @widths
 * @var string|array $class
 * @var Kirby\Cms\File|Kirby\Filesystem\Asset $image
 * @var string $sizes
 * @var bool|null $lazy
 * @var bool|null $clientBlur
 * @var string $alt
 * */

$clientBlur ??= true;
$attr ??= [];
$defaultWidths = [400, 800, 1000, 1200];

if (is_a($image, 'Kirby\Cms\File') || is_a($image, 'Kirby\Filesystem\Asset')) :
  $hasLazy = $lazy ?? true;
  $isSvg = $image->extension() == 'svg'; ?>

  <picture <?= attr([
              'class' => isset($class) ? (is_array($class) ? implode(' ', $class) : $class) : null,
              'style' => '--ratio: ' . $image->ratio() . ';',
            ]) ?>>
    <?php if ($isSvg) : ?>
      <?= svg($image) ?>
      <?php else :
      foreach ($formats ?? ['webp', $image->extension()] as $format) :
        $srcset = [];
        $median = $image->resize(median($widths ?? $defaultWidths));
        foreach ($widths ?? $defaultWidths as $width) :
          $srcset[$width . 'w'] = ['width' => $width, 'format' => $format];
        endforeach; ?>
        <?php if ($hasLazy) : ?>
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
      <?php endif;
      endforeach; ?>
      <img <?= attr([
              'data-thumbhash' => $clientBlur ? $image->th() : null,
              'src' => $clientBlur ? "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" : $image->thUri(),
              'data-src' => $median->url(),
              'width' => $image->width(),
              'height' => $image->height(),
              'alt' => $alt ?? is_a($image, 'Kirby\Cms\File') ? $image->alt() : null,
              'loading' => $hasLazy ? "lazy" : null,
              'data-sizes' => $sizes ?? 'auto',
              // 'style' => 'background-color: ' . $image->bhColor() . ';', // to be replaced and togglable
            ]) ?>>
    <?php endif ?>
  </picture>
<?php else : ?>
  <picture <?= attr(['class' => isset($class) ? (is_array($class) ? implode(' ', $class) : $class) : null]) ?>></picture>
<?php endif ?>