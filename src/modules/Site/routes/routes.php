<?php

return [
  [
    'name' => 'login',
    'url' => '/',
    'controller' => 'SiteController@index',
    'methods' => ['GET'],
    'isProtected' => false,
  ],
  [
    'name' => 'users',
    'url' => '/users{/:page}',
    'controller' => 'SiteController@users',
    'methods' => ['GET'],
    'params' => [
      'page' => '[0-9]+'
    ]
  ],
  [
    'name' => 'user',
    'url' => '/user/:userId',
    'controller' => 'SiteController@user',
    'methods' => ['GET'],
    'params' => [
      'userId' => '[0-9]+'
    ]
  ],
];