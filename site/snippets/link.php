<?php

/**
 * @var Kirby\Content\Field $link
 */ ?>

href="<?= $link->toUrl() ?>" <?php e($link->linkType() == 'url', 'target="_blank" rel="noopener noreferer me"') ?> <?php e(isset($title) && $title->isNotEmpty(), 'title="' . ($title ?? '') . '"') ?>