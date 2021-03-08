<?php

return [
  'debug' => true,
  'cache' => false,

  'url' => function () {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] === 'coralic-kirby') {
      return $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://' . $_SERVER['HTTP_X_FORWARDED_HOST'];
    }

    return false;
  }
];
