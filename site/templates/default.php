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
</head>

<body>
  <h1><?= $page->title() ?></h1>
  <canvas id="canvas" height="800" width="900"></canvas>
  <?= js('dist/dev/js/index.js') ?>
</body>

</html>