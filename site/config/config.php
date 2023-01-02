<?php

require_once __DIR__ . '/../plugins/kirby3-dotenv/global.php';

loadenv([
  'dir' => realpath(__DIR__ . '/../../'),
  'file' => '.env',
]);

return [
  'debug' => json_decode(env("KIRBY_DEBUG")),
  'cache' => [
    'pages' => [
      'active' => json_decode(env('KIRBY_CACHE')),
      'ignore' => function () {
        return kirby()->user() ?: false;
      }
    ]
  ],
  'date.handler' => 'strftime',
  // 'languages' => true, -> no panel language view
  'languages.detect' => true,
  'kirby-extended' => [
    'vite' => [
      'entry' => 'index.ts',
      'devServer' => 'http://localhost:' . env('VITE_DEV_PORT') ?? '3001',
    ]
  ],
  'bnomei' => [
    'dotenv' => [
      'dir' => function (): string {
        return realpath(kirby()->roots()->index() . '/../');
      },
    ]
  ],
  'routes' => [
    [
      'pattern' => 'sitemap.xml',
      'method' => 'GET',
      'action'  => function () {
        $feed = site()->index()->listed()->limit(50000)->sitemap(['images' => false, 'videos' => false]);
        return $feed;
      }
    ],
    [
      'pattern' => 'sitemap.xsl',
      'method' => 'GET',
      'action'  => function () {
        snippet('feed/sitemapxsl');
        die;
      }
    ],
  ],
];
