<?php

use Kirby\Toolkit\A;

if (isset($type)) : ?>
	<svg <?= attr(A::merge($attr ?? [], [
					'aria-hidden' => true,
					'class' => cls($class ?? 'icon')
				])) ?>>
		<use href="<?= vite()->asset('assets/sprite.svg') ?><?php e(vite()->isRunningHot(), "?t={$type}") ?>#<?= $type ?>"></use>
	</svg>
<?php endif ?>