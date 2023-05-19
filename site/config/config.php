<?php

use LukasKleinschmidt\Vite;

require_once __DIR__ . '/../plugins/kirby3-dotenv/global.php';

loadenv([
  'dir' => realpath(__DIR__ . '/../../'),
  'file' => '.env',
]);

return [
  'routes' => require_once __DIR__ . '/routes.php',
  'debug' => json_decode(env("KIRBY_DEBUG")),
  'cache.pages' => [
    'active' => json_decode(env('KIRBY_CACHE')),
    'ignore' => fn () => kirby()->user() ?: false,
  ],
  'yaml.handler' => 'symfony',
  'date.handler' => 'intl',
  'languages.detect' => true,
  'bnomei.dotenv.dir' => fn () => realpath(kirby()->roots()->index() . '/../'),
  'tobimori.seo' => [
    'robots' => [
      'pageSettings' => false,
      'sitemap' => 'https://' . $_SERVER['HTTP_HOST'] . '/sitemap.xml'
    ]
  ],
  'thathoff.sentry' => [
    'dsn' => env('SENTRY_DSN'),
    'environment' => json_decode(env("KIRBY_DEBUG")) ? 'development' : 'production',
  ],
  'lukaskleinschmidt.kirby-laravel-vite' => [
    'buildDirectory' => 'dist'
  ]
];
