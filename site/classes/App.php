<?php

namespace App;

use Kirby\Cms\App as Kirby;
use Kirby\Image\Darkroom;

class App extends Kirby
{
	public function __construct(array $props = [], bool $setInstance = true)
	{
		Darkroom::$types['baukasten'] = Image\Darkroom::class;

		parent::__construct($props, $setInstance);
	}

	/**
	 * Registers the Panel bundle that kirbyup builds from `src/panel` to `site/panel`
	 */
	protected function pluginsLoader(): array
	{
		static::plugin('project/panel') ?? static::plugin('project/panel', extends: [], root: $this->root('site') . '/panel');

		return parent::pluginsLoader();
	}
}
