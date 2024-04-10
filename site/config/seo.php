<?php

return [
	'robots.pageSettings' => false,
	'canonicalBase' => env("APP_URL"),
	'files' => [
		'parent' => 'site.find("page://images")',
		'template' => 'image'
	],
	'socialMedia' => [
		'twitter' => false,
		'youtube' => false
	],
	'sitemap' => [
		'excludeTemplates' => [
			'plugin-showcase-entry',
			'plugin-docs',
			'plugin-docs-category',
			'plugin-product'
		]
	]
];
