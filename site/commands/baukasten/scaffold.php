<?php

use Kirby\CLI\CLI;
use Kirby\Cms\Page;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;

return [
	'description' => 'Scaffold kirby-baukasten: Create initial content',
	'args' => [],
	'command' => static function (CLI $cli): void {
		kirby()->impersonate('kirby');
		$root = kirby()->roots()->base();
		$cli->info('Scaffolding kirby-baukasten...');

		if (!F::exists($root . '/.env')) {
			$cli->info('Copying .env file...');
			F::copy($root . '/.env.example', $root . '/.env');
		}

		if (!page('home')) {
			$cli->info('Creating empty home page...');
			$page = Page::create([
				'slug' => 'home',
				'template' => 'home',
				'content' => [],
			]);
			$page->changeStatus('listed');
		}

		if (!page('page://images')) {
			$cli->info('Creating images page...');
			$page = Page::create([
				'slug' => 'images',
				'template' => 'images',
				'content' => [
					'uuid' => 'images',
				],
			]);
			$page->changeStatus('unlisted');
		}

		if (F::exists($root . '/src/index.with-taxi.ts')) {
			$input = $cli->input('Do you want to use Taxi.js for page transitions?');
			$input->accept(['y', 'N'], true);
			$input->defaultTo('n');
			$response = $input->prompt();
			if ($response === 'y') {
				F::remove($root . '/src/index.ts');
				F::move($root . '/src/index.with-taxi.ts', $root . '/src/index.ts');
			} else {
				F::remove($root . '/src/index.with-taxi.ts');
				Dir::remove($root . '/src/renderers');
				Dir::remove($root . '/src/transition');
				shell_exec('pnpm remove @unseenco/taxi');
			}
		}

		$cli->info('Scaffolding done!');
	}
];
