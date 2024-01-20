<?php

use tobimori\Spielzeug\Menu;

require_once __DIR__ . '/../plugins/kirby3-dotenv/global.php';

loadenv([
	'dir' => realpath(__DIR__ . '/../../'),
	'file' => '.env',
]);

return [
	'debug' => json_decode(env('KIRBY_DEBUG')),
	'routes' => require_once __DIR__ . '/routes.php',
	'yaml.handler' => 'symfony',
	'date.handler' => 'intl',
	/** Image presets */
	'thumbs' => [
		'srcsets' => [
			'small' => [300, 500, 800],
			'default' => [400, 800, 1000, 1200],
			'large' => [800, 1400, 2000, 2600],
		]
	],
	/** SEO */
	'url' => env("APP_URL"),
	'tobimori.seo' => [
		'robots.pageSettings' => false,
		'canonicalBase' => env("APP_URL"),
		'files' => [
			'parent' => 'site.find("page://images")',
			'template' => 'image'
		],
		'socialMedia' => [
			'twitter' => false,
			'youtube' => false
		]
	],
	/** Email */
	'email' => [
		'transport' => [
			'type' => 'smtp',
			'host' => env("KIRBY_MAIL_HOST"),
			'port' => json_decode(env("KIRBY_MAIL_PORT")),
			'security' => true,
			'auth' => 'tls',
			'username' => env("KIRBY_MAIL_USER"),
			'password' => env("KIRBY_MAIL_PASS")
		]
	],
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
	'bnomei.lapse.cache' => ['type' => 'apcugc'],
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
			'favicon' => option('debug') ? 'assets/panel/favicon-dev.svg' : 'assets/panel/favicon-live.svg',
			'css' => vite('src/styles/panel.css'),
		],
	]
];
