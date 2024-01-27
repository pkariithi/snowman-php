<?php

return [
  'base' => [
    'date.timezone' => 'Africa/Nairobi',
    'default_charset' => 'UTF-8',
    'display_errors' => 'On',
    'expose_php' => 'On',
    'log_errors' => 1,
    'error_log' => SRC_DIR.'var'.DS.'log'.DS.'error.log',
    'max_execution_time' => 7200,
    'max_input_time' => 200,
    'memory_limit' => '32M',
    'mysql.connect_timeout' => 20,
    'session.auto_start' => 'Off',
    'session.use_only_cookies' => true,
    'session.use_cookies' => 'On',
    'session.use_trans_sid' => 'Off',
    'session.cookie_httponly' => 'On',
    'session.cookie_lifetime' => 24 * 60 * 60,
    'session.gc_maxlifetime' => 24 * 60 * 60,
    'session.gc_probability' => 1,
    'session.gc_divisor' => 100,
    'upload_max_filesize' => '20M',
  ],
  'env' => [
    'dev' => [],
    'uat' => [],
    'prod' => [
      'display_errors' => 'Off',
      'expose_php' => 'Off',
      'memory_limit' => '256M',
    ]
  ]
];