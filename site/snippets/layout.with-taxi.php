<?php

/** @var Kirby\Cms\Page $page
 *  @var Kirby\Cms\Site $site
 *  @var Kirby\Cms\App $kirby */

use Kirby\Filesystem\F;

if (json_decode(env('REQUIRES_LOGIN')) && !$kirby->user()) {
	go('/panel');
} ?>

<!DOCTYPE html>
<html lang="<?= $kirby->language()?->code() ?>">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<?php snippet('seo/head') ?>

	<link rel="icon" href="/favicon.svg" />
	<link rel="mask-icon" href="/favicon.svg" color="#000000" />
	<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
	<meta name="theme-color" content="#000000">

	<?= vite([
		'src/styles/index.css', 'src/index.ts'
	]) ?>
</head>

<body class="min-h-screen antialiased overflow-x-clip">
	<div data-taxi>
		<div class="flex flex-col" data-taxi-view>
			<?php snippet('core/skip-nav') ?>
			<?php snippet('core/nav') ?>
			<main class="container flex-grow">
				<div id="main"></div>
				<?= $slot ?>
			</main>
			<?php snippet('core/footer') ?>
		</div>
	</div>
	<?php snippet('seo/schemas') ?>
	<script>
		console.log('%ctobimori/kirby-baukasten<?= F::exists($kirby->roots()->base() . '/.git/refs/heads/main') ? '#' . substr(file_get_contents($kirby->roots()->base() . '/.git/refs/heads/main'), 0, 7) : '' ?>', 'font-size: 12px; font-weight: bold; color: #F9E7FF; background-color: #7D3BA8; padding: 10px 16px; margin: 20px 0; border-radius: 4px;')
		console.log('%cMade with ♥ in Germany by Tobias Möritz', 'font-size: 12px; font-weight: bold; color: #0e1f28; background-color: #fff; padding: 10px 16px; margin: 20px 0; border-radius: 4px;')
	</script>
</body>

</html>
