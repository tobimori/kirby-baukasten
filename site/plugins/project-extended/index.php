<?php

use Kirby\Cms\App;
use Kirby\Data\Yaml;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Toolkit\A;

App::plugin('project/extended', [
	'blueprints' => [
		'programmatic/admin-tools' => function (App $kirby) {
			if (($user = $kirby->user()) && $user->role()->name() === 'admin') {
				return Yaml::decode(F::read(dirname(__DIR__, 2) . '/blueprints/tabs/admin-tools.yml'));
			}
		}
	],
	/**
	 * Automatically load translations from `site/translations` folder
	 */
	'translations' => A::keyBy(A::map(
		Dir::read($dir = dirname(__DIR__, 2) . '/translations'),
		fn ($file) => A::merge(['lang' => F::name($file)], Yaml::decode(F::read($dir . '/' . $file)))
	), 'lang')
]);
