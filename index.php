<?php

use Kirby\Cms\App;

require 'kirby/bootstrap.php';

// fix dev server for Kirby 3.6.0
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] === 'coralic-kirby') {
  $_SERVER['SERVER_NAME'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
  $_SERVER['SERVER_PORT'] = $_SERVER['HTTP_X_FORWARDED_PORT'];
}

echo (new App)->render();
