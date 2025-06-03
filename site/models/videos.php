<?php

class VideosPage extends FilesPage
{
	/**
	 * Override the page title to be static
	 * to the template name
	 */
	public function title(): Field
	{
		return new Field($this, 'title', t('Videos'));
	}
}
