<?php

require_once dirname(__DIR__) . '/plugins/kirby3-dotenv/global.php';

loadenv([
	'dir' => realpath(dirname(__DIR__, 2)),
	'file' => '.env',
]);

return [
	'debug' => json_decode(env('KIRBY_DEBUG')),
	'routes' => require __DIR__ . '/routes.php',
	'thumbs' => require __DIR__ . '/thumbs.php',
	'hooks' => require __DIR__ . '/hooks.php',
	'yaml.handler' => 'symfony',
	'date.handler' => 'intl',
	'url' => env("APP_URL"),
	'tobimori.trawl' => [
		'sourceLanguage' => 'en',
		'languages' => ['de', 'en'],
		'include' => [
			'site/templates/**/*.php',
			'site/snippets/**/*.php',
			'site/models/**/*.php',
			'site/blueprints/**/*.yml',
			'site/config/**/*.php',
		]
	],
	'tobimori.seo' => require __DIR__ . '/seo.php',
	'tobimori.dreamform' => require __DIR__ . '/dreamform.php',
	'tobimori.tailwind-merge.config' => [
		'theme' => [
			'text' => ['body', 'small'],
			'spacing' => ['container'],
		],
	],
	'tobimori.icon-field' => [
		// use generated sprite, requires pnpm run build
		// to generate (dev sprite doesn't work since it's not in the public folder)
		'folder' => '',
		'sprite' => fn() => spritePath()
	],
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
	'cache' => [
		'pages' => [
			'active' => json_decode(env('KIRBY_CACHE')),
			'type' => 'static',
			'compression' => ['gzip' => 9],
			'root' => dirname(__DIR__, 2) . '/public/__staticache',
			'prefix' => null
		]
	],
	/** Build Env / Vite / etc. */
	'bnomei.dotenv.dir' => fn() => realpath(kirby()->roots()->base()),
	'lukaskleinschmidt.kirby-laravel-vite.buildDirectory' => 'dist',
	/** Panel */
	'johannschopplich.plausible.sharedLink' => env('PLAUSIBLE_SHARED_LINK'),
	'distantnative' => [
		'retour' => [
			'config' =>  dirname(__DIR__, 2) . '/data/storage/retour/config.yml',
			'database' =>  dirname(__DIR__, 2) . '/data/storage/retour/log.sqlite'
		]
	],
	'panel' => [
		'vue' => [
			'compiler' => false
		],
		'menu' => function () {
			$menu = panelMenu()->site();

			foreach (kirby()->user()?->favorites()->toPages() ?? [] as $fav) {
				$menu->page($fav->title()->value(), $fav, ['icon' => 'star']);
			}

			$menu->separator();

			if ($images = page('page://images')) {
				$menu->page($images->title()->value(), $images, ['icon' => 'file-image']);
			}

			return $menu
				->separator()
				->area('users')
				->area('plausible')
				->area('retour')
				->area('queues')
				->toArray();
		}
	],
	'ready' => fn() => [
		'panel' => [
			'favicon' => option('debug') ? 'static/panel/favicon-dev.svg' : 'static/panel/favicon-live.svg',
			'css' => vite('src/styles/panel.css'),
		],
	]
];
