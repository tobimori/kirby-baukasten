<?php

/**
 * @var Kirby\Cms\StructureObject|Kirby\Content\Content $link
 */ ?>

<?php if ($link->link()->isNotEmpty()) : ?>
	<a <?= attr([
				'class' => $class ?? '',
				'href' => $link->link()->toUrl(),
				'target' => $link->newTab()->toBool() ? '_blank' : null,
				'rel' => $link->newTab()->toBool() ? 'noopener' : null,
				'aria-current' => $link->link()->linkType() === 'page' && $link->link()->toPage()?->isActive() ? 'page' : null,
				...($attr ?? [])
			]) ?>>
		<?= $slot ?? $link->label()->or($link->link()->linkTitle()) ?>
	</a>
<?php endif ?>