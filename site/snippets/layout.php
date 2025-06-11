<?php

/** @var Kirby\Cms\Page $page
 *  @var Kirby\Cms\Site $site
 *  @var Kirby\Cms\App $kirby */

if (json_decode(env('REQUIRES_LOGIN')) && !$kirby->user()) {
	go('/panel');
} ?>

<!DOCTYPE html>
<html lang="<?= $kirby->language()?->code() ?>">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<?php snippet('seo/head', slots: true) ?>

	<?= vite([
		'src/styles/index.css',
		'src/index.ts'
	]) ?>

	<?php endsnippet() ?>

	<link rel="icon" href="/favicon.svg" />
	<link rel="mask-icon" href="/favicon.svg" color="#000000" />
	<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
	<meta name="theme-color" content="#000000">
</head>

<body class="flex flex-col min-h-screen antialiased overflow-x-clip">
	<?php snippet('core/skip-nav') ?>
	<?php snippet('core/nav') ?>
	<main class="flex-grow">
		<div id="main"></div>
		<?= $slot ?>
	</main>
	<?php snippet('core/footer') ?>
	<?php snippet('seo/schemas') ?>
</body>

</html>