<?php

use Kirby\Toolkit\A;

if (isset($type)) : ?>
	<svg <?= attr(A::merge($attr ?? [], [
					'aria-hidden' => true,
					'class' => cls($class ?? 'shrink-0 text-current')
				])) ?>>
		<use href="<?= vite()->asset(vite()->isRunningHot() ? '__spritemap' : 'spritemap.svg') ?>#<?= $type ?>"></use>
	</svg>
<?php endif ?>