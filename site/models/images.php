<?php

use Kirby\Cms\Page;
use Kirby\Content\Field;

class ImagesPage extends Page
{
	/**
	 * Override the page title to be static
	 * to the template name
	 */
	public function title(): Field
	{
		return new Field($this, 'title', t($this->intendedTemplate()->name()));
	}
}
