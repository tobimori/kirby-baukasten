<?php

return [
	// site/classes/Image/Darkroom.php
	'driver' => 'baukasten',
	// Perceptual quality as SSIMULACRA 2 score (https://github.com/cloudinary/ssimulacra2), same for every format:
	// 90 visually lossless, 80 very high, 70 high, 50 medium. Each step of 5 costs about 30 % more bytes.
	// The image snippet puts it into the thumb filenames, so a change regenerates all thumbs.
	'quality' => 70,
	// AVIF encoder speed from 0 (slowest) to 9. Speed 8 needs 40 % less CPU time for about 10 % larger files
	'avifSpeed' => 6,
	// Converts wide-gamut images (Display P3, Adobe RGB) to sRGB. Set to false to keep their colors,
	// but only with ImageMagick 7: the AVIF encoder of ImageMagick 6 drops the profile, so the colors get dull
	'srgb' => true,
	// Threads for each thumb: with 4, thumbs are ready 25 to 40 % sooner while the server has free cores.
	// Without free cores there is no gain and about 5 % more CPU time, so use 1 on a fully loaded server
	'threads' => 4,
	'formats' => ['avif', 'webp'],
	'srcsets' => [
		'small' => [300, 500, 800],
		'default' => [400, 800, 1000, 1200],
		'large' => [800, 1400, 2000, 2600],
	]
];
