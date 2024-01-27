<?php

return [
  'base' => [
    'site' => [
      'name' => 'Snowman App',
      'slogan' => 'The Snowman',
      'year' => 2020,
      'lang' => 'en',
      'charset' => 'utf-8'
    ],
  ],
  'env' => [
    'dev' => [
      'site' => [
        'lang' => 'sw',
        'year' => date('Y')
      ],
    ],
    'uat' => [],
    'prod' => [],
  ]
];