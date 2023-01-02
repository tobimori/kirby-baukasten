<?php

/** 
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site 
 */

snippet('layout', slots: true) ?>

<h1><?= $page->title() ?></h1>

<?php endsnippet() ?>