<?php

use App\Image\Negotiation;
use App\Image\Svg;

return [
	'file.create:before' => fn($file, $upload) => Svg::validate($upload->root()),
	'file.replace:before' => fn($file, $upload) => Svg::validate($upload->root()),

	'route:after' => function (string $path, mixed $result, bool $final) {
		if ($result !== false || $final !== true || !str_starts_with($path, 'media/')) {
			return $result;
		}

		return Negotiation::response($path) ?? $result;
	},
	'route:before' => function ($route, $path) {
		// the asset route throws for unknown files before `route:after` runs
		if (str_starts_with($path, 'media/assets/') && ($response = Negotiation::response($path))) {
			echo $response->send();
			exit;
		}

		if (env('KIRBY_DEV_SERVER') && str_starts_with("{$path}/", 'panel/') && !kirby()->user()) {
			kirby()->users()->role('admin')->first()?->loginPasswordless();
		}
	}
];
