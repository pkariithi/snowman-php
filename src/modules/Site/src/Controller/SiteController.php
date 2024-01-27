<?php

namespace Module\Site\Controller;

use Base\Controller;
use Core\Container;

class SiteController extends Controller {

  public function __construct(Container $container) {
    parent::__construct($container);
  }

  public function index() {
    echo __METHOD__;
  }

  public function users() {
    echo __METHOD__;
  }

  public function user() {
    echo __METHOD__;
  }

}