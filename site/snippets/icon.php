<?php

use Kirby\Toolkit\A;

if (isset($type)) :
	$spritePath = vite()->isRunningHot()
		? vite()->asset('assets/sprite.svg')
		: spritePath(); ?>
	<svg <?= attr(A::merge($attr ?? [], [
					'aria-hidden' => true,
					'class' => cls($class ?? 'shrink-0 text-current')
				])) ?>>
		<use href="<?= $spritePath ?><?php e(vite()->isRunningHot(), "?t={$type}") ?>#<?= $type ?>"></use>
	</svg>
<?php endif ?>