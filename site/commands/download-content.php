<?php

use Kirby\CLI\CLI;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Uuid\Uuid;

return [
	'description' => 'Download Content as ZIP',
	'args' => [],
	'command' => static function (CLI $cli): void {
		// remove old backups
		if (Dir::exists(kirby()->roots()->index() . '/backups')) Dir::remove(kirby()->roots()->index() . '/backups');

		// create zip
		$path = '/backups/' . Uuid::generate() . '.zip';
		janitor()->command('janitor:backupzip --quiet --output public' . $path);
		$backup = janitor()->data('janitor:backupzip');

		if (F::exists($backup['path'])) {
			// download in browser
			janitor()->data(
				$cli->arg('command'),
				array_merge(
					[
						'status' => 200,
						'download' => site()->url() . $path
					],
					$backup
				)
			);
		}
	}
];
