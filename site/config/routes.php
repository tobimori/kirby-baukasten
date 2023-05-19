<?php

return [
  [
    'pattern' => 'sitemap.xml',
    'method' => 'GET',
    'action' => fn () => site()->index()->listed()->limit(50000)->sitemap(['images' => false, 'videos' => false]),
  ],
  [
    'pattern' => 'sitemap.xsl',
    'method' => 'GET',
    'action'  => function () {
      snippet('feed/sitemapxsl');
      die;
    }
  ],
];
