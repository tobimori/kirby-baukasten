<?php

/** @var Kirby\Cms\Page $page */ ?>

<!DOCTYPE html>
<html lang="de">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Meta Knight -->
  <?php snippet('meta_information', ['page' => $page]) ?>
  <?php snippet('robots', ['page' => $page]) ?>
  <!-- Icons -->
  <link rel="icon" href="/favicon.svg" />
  <link rel="mask-icon" href="/favicon.svg" color="#000000" />
  <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
  <meta name="theme-color" content="#000000">
  <!-- Styles -->
  <?= loadCSS('index.css') ?>
</head>

<body>
  <div class="s-content -type-<?= $page->template() ?>">