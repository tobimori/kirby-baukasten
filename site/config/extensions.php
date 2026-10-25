<?php

use App\Models\FilesPage;
use App\Models\ImagesPage;
use App\Models\VideosPage;
use Kirby\Content\Field;
use Kirby\Http\Url;
use Kirby\Toolkit\Str;

// https://getkirby.com/docs/reference/plugins/extensions
return [
	'pageModels' => [
		'files' => FilesPage::class,
		'images' => ImagesPage::class,
		'videos' => VideosPage::class,
	],
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
];
