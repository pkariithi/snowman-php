<?php

namespace Module\Error\Controller;

use Base\Controller;
use Core\Container;

class ErrorController extends Controller {

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function e401() {
    echo __METHOD__;
  }

  public function e404() {
    echo __METHOD__;
  }

  public function e500() {
    echo __METHOD__;
  }

}