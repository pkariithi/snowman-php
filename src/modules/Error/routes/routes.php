<?php

return [
  [
    'name' => '401',
    'url' => '/401',
    'controller' => 'ErrorController@e401',
    'methods' => ['GET'],
    'isProtected' => false,
  ],
  [
    'name' => '404',
    'url' => '/404',
    'controller' => 'ErrorController@e404',
    'methods' => ['GET'],
    'isProtected' => false,
  ],
  [
    'name' => '500',
    'url' => '/500',
    'controller' => 'ErrorController@e500',
    'methods' => ['GET'],
    'isProtected' => false,
  ],
];