<?php

use Kirby\Cms\Page;
use Kirby\Content\Field;

class FilesPage extends Page
{
	public function metaDefaults()
	{
		return [
			'robotsIndex' => false
		];
	}

	/**
	 * Override the page title to be static
	 * to the template name
	 */
	public function title(): Field
	{
		return new Field($this, 'title', t("page.{$this->intendedTemplate()->name()}.title"));
	}
}
