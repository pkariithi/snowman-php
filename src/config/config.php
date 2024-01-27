<?php

return [
  'base' => [
    'site' => [
      'name' => 'Snowman App',
      'lang' => 'en',
      'charset' => 'utf-8',
    ],
    'database' => [
      'active' => 'mysql',
      'mysql' => [
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'name' => 'mod',
      ]
    ],
    'security' => [
      'is_2fa_enabled' => false,
      '2fa_otp_channel' => 'sms',
      '2fa_otp_length' => 4,
      '2fa_otp_expiration_in_sec' => 5 * 60,
      '2fa_otp_max_attempts' => 3,
    ],
    'session' => [
      'name' => 'modphp',
      'allow_multiple_logins' => true,
      'session_timeout_in_sec' => 48 * 60 * 60,
      'session_timeout_message' => 'Your session is about to expire. Kindly reload the page or browse to another link to stay logged in.',
    ],
    'email' => [
      'host' => '',
      'name' => '',
      'pass' => '',
      'port' => '',
      'from' => '',
    ],
    'log' => [
      'enabled' => true,
      'path' => SRC_DIR.'var'.DS.'log'.DS,
      'levels' => ['debug','info','warn','error'],
      'level' => 'debug',
    ]
  ],
  'env' => [
    'dev' => [],
    'uat' => [
      'log' => [
        'level' => 'warn'
      ]
    ],
    'prod' => [
      'log' => [
        'level' => 'error'
      ]
    ],
  ]
];