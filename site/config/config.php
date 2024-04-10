<?php

use tobimori\Spielzeug\Menu;

require_once dirname(__DIR__) . '/plugins/kirby3-dotenv/global.php';

loadenv([
	'dir' => realpath(dirname(__DIR__, 2)),
	'file' => '.env',
]);

return [
	'debug' => json_decode(env('KIRBY_DEBUG')),
	'routes' => require_once __DIR__ . '/routes.php',
	'yaml.handler' => 'symfony',
	'date.handler' => 'intl',
	'thumbs' => require __DIR__ . '/thumbs.php',
	/** SEO */
	'url' => env("APP_URL"),
	'tobimori.seo' => require __DIR__ . '/seo.php',
	/** Email */
	'email' => require __DIR__ . '/email.php',
	'auth' => [
		'methods' => ['password', 'password-reset'],
		'challenge' => [
			'email' => [
				'from' => env("KIRBY_MAIL_FROM"),
				'subject' => 'Login-Code'
			]
		]
	],
	/** Caching */
	'cache.pages' => [
		'active' => json_decode(env('KIRBY_CACHE')),
		'type' => 'static',
		'root'   =>  __DIR__ . '/../../public/__staticache/',
		'prefix' => null
	],
	/** Build Env / Vite / etc. */
	'bnomei.dotenv.dir' => fn () => realpath(kirby()->roots()->base()),
	'lukaskleinschmidt.kirby-laravel-vite.buildDirectory' => 'dist',
	'distantnative.retour.config' => 'data/storage/retour.yml',
	/** Panel */
	'panel.menu' => fn () => [
		'site' => Menu::site(),
		'-',
		'images' => Menu::page(null, 'images', page('page://images')),
		'-',
		'users',
		'retour'
	],
	'ready' => fn () => [
		'panel' => [
			'favicon' => option('debug') ? 'panel/favicon-dev.svg' : 'panel/favicon-live.svg',
			'css' => vite('src/styles/panel.css'),
		],
	]
];
