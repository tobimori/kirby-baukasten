<?php

use Kirby\Cms\App;
use Kirby\Content\Field;
use Kirby\Http\Url;
use Kirby\Toolkit\Str;

App::plugin('project/extended', [
	'fieldMethods' => [
		'linkType' => function (Field $field) {
			$val = $field->value();
			if (empty($val)) return 'custom';

			if (Str::match($val, '/^(http|https):\/\//')) return 'url';
			if (Str::startsWith($val, 'page://') || Str::startsWith($val, '/@/page/')) return 'page';
			if (Str::startsWith($val, 'file://') || Str::startsWith($val, '/@/file/')) return 'file';
			if (Str::startsWith($val, 'tel:')) return 'tel';
			if (Str::startsWith($val, 'mailto:')) return 'email';
			if (Str::startsWith($val, '#')) return 'anchor';

			return 'custom';
		},
		'linkTitle' => function (Field $field) {
			$val = $field->value();

			return match ($field->linkType()) {
				'url' => Url::short($val),
				'page' => $field->toPage()?->title()->value() ?? $val,
				'file' => $field->toFile()?->filename() ?? $val,
				'email' => Str::replace($val, 'mailto:', ''),
				'tel' => Str::replace($val, 'tel:', ''),
				default => $val
			};
		},
	],
]);
