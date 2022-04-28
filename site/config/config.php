<?php

@include __DIR__ . DS . 'credentials.php';

return [
  'debug' => true,
  'date.handler' => 'strftime',

  'kirby-extended' => [
    'vite' => [
      'entry' => 'index.ts',
      'devServer' => 'http://localhost:3001'
    ]
  ]
];
