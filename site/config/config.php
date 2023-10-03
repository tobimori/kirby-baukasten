<?php

use tobimori\Spielzeug\Menu;

require_once __DIR__ . '/../plugins/kirby3-dotenv/global.php';

loadenv([
	'dir' => realpath(__DIR__ . '/../../'),
	'file' => '.env',
]);

return [
	'debug' => true,
	'routes' => require_once __DIR__ . '/routes.php',
	'yaml.handler' => 'symfony',
	'date.handler' => 'intl',
	'tobimori.seo' => [
		'robots' => [
			'pageSettings' => false,
			'sitemap' => 'https://' . $_SERVER['HTTP_HOST'] . '/sitemap.xml'
		],
		'files' => [
			'parent' => 'site.find("page://images")',
			'template' => 'image'
		],
		'socialMedia' => [
			'twitter' => false,
			'youtube' => false
		]
	],
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
	'cache.pages' => [
		'active' => json_decode(env('KIRBY_CACHE')),
	],
	/** Build Env / Vite / etc. */
	'bnomei.dotenv.dir' => fn () => realpath(kirby()->roots()->base()),
	'lukaskleinschmidt.kirby-laravel-vite.buildDirectory' => 'dist',
	'ready' => fn () => [
		'panel' => [
			'css' => vite('src/styles/panel.css'),
			'menu' => [
				'site' => Menu::site('overview'),
				'-',
				'images' => Menu::page('images', 'images', 'pages/images'),
				'-',
				'users',
				'retour'
			],
		],
	]
];
