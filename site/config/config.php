<?php

@include __DIR__ . DS . 'credentials.php';

return [
  'debug' => true,
  'date.handler' => 'strftime',

  'kirby-extended' => [
    'vite' => [
      'entry' => 'index.ts'
    ]
  ]
];
