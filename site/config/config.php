<?php

Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2), ['.env', '.env.local'], false)->safeLoad();

if (!function_exists('env')) {
	function env(string $key, mixed $default = null): mixed
	{
		$value = getenv($key);
		return $value !== false ? $value : $_ENV[$key] ?? $_SERVER[$key] ?? $default;
	}
}

return [
	'debug' => json_decode(env('KIRBY_DEBUG')),
	'routes' => require __DIR__ . '/routes.php',
	'thumbs' => require __DIR__ . '/thumbs.php',
	'hooks' => require __DIR__ . '/hooks.php',
	'extensions' => require __DIR__ . '/extensions.php',
	'yaml.handler' => 'symfony',
	'date.handler' => 'intl',
	'url' => env('KIRBY_DEV_SERVER') ? '*' : env('APP_URL'),
	'session' => ['cookieName' => env('KIRBY_SESSION_COOKIE', 'kirby_session')],
	'content' => ['salt' => env('CONTENT_SALT')],
	'tobimori.trawl' => [
		'sourceLanguage' => 'en',
		'languages' => ['de', 'en'],
		'include' => [
			'site/templates/**/*.php',
			'site/snippets/**/*.php',
			'site/classes/**/*.php',
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
		// reads the built sprite from disk, requires pnpm run build
		'folder' => '',
		'sprite' => fn() => Url::path(LukasKleinschmidt\Vite::copy()->useHotFile('.never')->asset('spritemap.svg'))
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
	'lukaskleinschmidt.kirby-laravel-vite' => [
		'buildDirectory' => 'dist',
	],
	/** Panel */
	'johannschopplich.plausible' => [
		'sharedLink' => env('PLAUSIBLE_SHARED_LINK'),
	],
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
