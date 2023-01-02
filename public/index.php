<?php

use Kirby\Cms\App;

define("KIRBY_HELPER_DUMP", false);

$base = dirname(__DIR__);

require $base . '/kirby/bootstrap.php';
require $base . '/vendor/autoload.php';

$kirby = new App([
  'roots' => [
    'index'    => __DIR__,
    'base'     => $base,
    'site'     => $base . '/site',
    'storage'  => $storage = $base . '/storage',
    'content'  => $storage . '/content',
    'accounts' => $storage . '/accounts',
    'cache'    => $storage . '/cache',
    'logs'     => $storage . '/logs',
    'sessions' => $storage . '/sessions',
    'license'  => $storage . '/.license'
  ]
]);

echo $kirby->render();
