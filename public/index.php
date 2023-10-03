<?php

use Kirby\Cms\App;

/**
 * Smart version of echo with an if condition as first argument
 *
 * @param mixed $value The string to be echoed if the condition is true
 * @param mixed $alternative An alternative string which should be echoed when the condition is false
 */
function e(mixed $condition, mixed $value, mixed $alternative = null): void
{
	echo $condition ? $value : $alternative;
}

define('KIRBY_HELPER_E', false);
define('KIRBY_HELPER_ATTR', false);
define("KIRBY_HELPER_DUMP", false);

$base = dirname(__DIR__);

require $base . '/kirby/bootstrap.php';
require $base . '/vendor/autoload.php';

$kirby = new App([
	'roots' => [
		'index'    => __DIR__,
		'base'     => $base,
		'site'     => $base . '/site',
		'storage'  => $storage = $base . '/storage',
		'content'  => $storage . '/content',
		'accounts' => $storage . '/accounts',
		'cache'    => $storage . '/cache',
		'logs'     => $storage . '/logs',
		'sessions' => $storage . '/sessions',
		'license'  => $storage . '/.license'
	]
]);

echo $kirby->render();
