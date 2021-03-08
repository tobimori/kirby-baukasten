<?php

/** 
 * @var Kirby\Cms\App $kirby
 * @var Kirby\Cms\Page $page
 * @var Kirby\Cms\Site $site 
 */

/** 
 * Declare more variable types as you need them,
 * more information can be found in README.md.
 */

?>

<!DOCTYPE html>
<html>

<head>
  <?php snippet('dist-files') ?>
</head>

<body>
  <h1><?= $page->title() ?></h1>
</body>

</html>