<?php

namespace Base;

use Core\Container;

class Controller {

  protected $container;
  protected $config;
  protected $request;
  protected $response;
  protected $logger;
  protected $flash;
  protected $session;

  public function __construct(Container $container) {
    $this->container = $container;
    $this->config = $this->container->get('config');
    $this->request = $this->container->get('request');
    $this->response = $this->container->get('response');
    $this->logger = $this->container->get('logger');
    $this->flash = $this->container->get('flash');
    $this->session = $this->container->get('session');
  }

}