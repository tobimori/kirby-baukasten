<?php

/**
 * @var Kirby\Cms\StructureObject|Kirby\Content\Content $link
 */ ?>

<?php if ($link->link()->isNotEmpty() || $link->anchor()->isNotEmpty()) : ?>
	<?php $href = $link->link()->isNotEmpty() ? $link->link()->toUrl() : '';
	$href .= $link->anchor()->isNotEmpty() ? '#' . $link->anchor() : ''; ?>
	<a <?= attr([
				'class' => $class ?? '',
				'href' => $href,
				'target' => $link->newTab()->toBool() ? '_blank' : null,
				'rel' => $link->newTab()->toBool() ? 'noopener' : null,
				'aria-current' => $link->link()->isNotEmpty() && $link->link()->linkType() === 'page' && $link->link()->toPage()?->isActive() ? 'page' : null,
				...($attr ?? [])
			]) ?>>
		<?= $slot ?? $link->label()->or($link->link()->isNotEmpty() ? $link->link()->linkTitle() : '') ?>
	</a>
<?php endif ?>
