<?php

use Kirby\Cms\App as Kirby;

Kirby::plugin('tobimori/baukasten', [
  'translations' => [
    'de' => [
      'email.placeholder' => 'mail@coralic.com'
    ],
    'en' => [
      'email.placeholder' => 'mail@coralic.com'
    ],
  ],
  'icons' => [],
  'blockMethods' => [
    'attr' => function ($attr) {
      $attr = array_merge_recursive($attr, ['class' => ['o-block'], 'data-block-id' => $this->id()]);

      if ($this->blockId()->isNotEmpty()) {
        $attr['id'] = $this->blockId();
      }

      $attr['data-prev-block'] = $this->prev() ? $this->prev()->type() : 'navigation';
      $attr['data-next-block'] = $this->next() ? $this->next()->type() : 'footer';

      $s = "";
      foreach ($attr as $key => $value) {
        if (is_array($value)) {
          $value = implode(' ', $value);
        }

        $s .= $key . '="' . $value . '" ';
      }

      return $s;
    }
  ],
]);

/**
 * Returns the median of an array
 * 
 * @param array $array
 * @return float
 */
function median($array)
{
  $count = count($array);
  if ($count == 0) {
    return 0;
  }
  $middle = floor(($count - 1) / 2);
  sort($array, SORT_NUMERIC);
  if ($count % 2) {
    return $array[$middle];
  } else {
    return ($array[$middle] + $array[$middle + 1]) / 2;
  }
}
