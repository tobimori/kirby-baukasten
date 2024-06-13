<?php

use Kirby\Toolkit\A;

if (isset($type)) : ?>
	<svg <?= attr(A::merge($attr ?? [], [
					'aria-hidden' => true,
					'class' => cls($class ?? 'icon')
				])) ?>>
		<use xlink:href="<?= vite()->asset('assets/sprite.svg') ?>#<?= $type ?>"></use>
	</svg>
<?php endif ?>