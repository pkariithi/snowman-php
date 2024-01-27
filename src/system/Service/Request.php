<?php

namespace Service;
use Helper\CleanData;

class Request {

  public $baseUrl;
  public $fullUrl;
  public $uri;

  public $isCron = false;
  public $isAjax = false;
  public $isAudit = false;
  public $isApi = false;

  protected $get = [];
  protected $post = [];
  protected $cookies = [];
  protected $files = [];
  protected $server = [];
  protected $stream = '';

  public function __construct($get, $post, $cookies, $files, $server, $stream) {

    if(!empty($get)) {
      $this->arrayWalkRecursiveReferential($get, [CleanData::class, 'preventXSS']);
      $this->arrayWalkRecursiveReferential($get, 'trim');
      $this->arrayWalkRecursiveReferential($get, [CleanData::class, 'escape']);
    }

    if(!empty($post)) {
      $this->arrayWalkRecursiveReferential($post, [CleanData::class, 'preventXSS']);
      $this->arrayWalkRecursiveReferential($post, 'trim');
      $this->arrayWalkRecursiveReferential($post, [CleanData::class, 'escape']);
    }

    $this->get = $get;
    $this->post = $post;
    $this->cookies = $cookies;
    $this->files = $files;
    $this->server = $server;
    $this->stream = $stream;

    $this->loadFullUrl();
  }

  public function getGetParam($key, $defaultValue = false) {
    if(array_key_exists($key, $this->get)) {
      return $this->get[$key];
    }
    return $defaultValue;
  }

  public function getPostParam($key, $defaultValue = false) {
    if(array_key_exists($key, $this->post)) {
      return $this->post[$key];
    }
    return $defaultValue;
  }

  public function getParam($key, $defaultValue = false) {
    if(array_key_exists($key, $this->post)) {
      return $this->post[$key];
    }
    if(array_key_exists($key, $this->get)) {
      return $this->get[$key];
    }
    return $defaultValue;
  }

  public function getFile($key, $defaultValue = false) {
    if(array_key_exists($key, $this->files)) {
      return $this->files[$key];
    }
    return $defaultValue;
  }

  public function getCookie($key, $defaultValue = false) {
    if(array_key_exists($key, $this->cookies)) {
      return $this->cookies[$key];
    }
    return $defaultValue;
  }

  public function getGetParams() {
    return $this->get;
  }

  public function getGetParamsAsUri() {
    if(empty($this->get)) {
      return null;
    }

    $uriArr = [];
    foreach($this->get as $k => $v) {
      $uriArr[] = $k.'='.$v;
    }
    return '?'.implode('&', $uriArr);
  }

  public function getPostParams() {
    return $this->post;
  }

  public function getAllParams() {
    return array_merge($this->get, $this->post);
  }

  public function getStream() {
    return $this->stream;
  }

  public function getCookies() {
    return $this->cookies;
  }

  public function getFiles() {
    return $this->files;
  }

  public function getUri() {
    $uri = $this->getServerVariable('REQUEST_URI');
    $exploded = explode('?', $uri);
    return filter_var($exploded[0], FILTER_SANITIZE_URL);
  }

  public function getPath() {
    $path = strtok($this->getUri(), '?');
    return empty($path) ? '/' : $path;
  }

  public function getMethod() {
    return $this->getServerVariable('REQUEST_METHOD');
  }

  public function getHttpAccept() {
    return $this->getServerVariable('HTTP_ACCEPT');
  }

  public function getReferrer() {
    return $this->getServerVariable('HTTP_REFERRER');
  }

  public function getUserAgent() {
    return $this->getServerVariable('HTTP_USER_AGENT');
  }

  public function getQueryString() {
    return $this->getServerVariable('QUERY_STRING');
  }

  public function isHttps() {
    return ($this->getServerVariable('HTTPS') === 'on');
  }

  public function getIpAddress() {
    if(array_key_exists('HTTP_CLIENT_IP', $this->server)) {
      return $this->server['HTTP_CLIENT_IP'];
    }

    if(array_key_exists('HTTP_X_FORWARDED_FOR', $this->server)) {
      return $this->server['HTTP_X_FORWARDED_FOR'];
    }

    if(array_key_exists('REMOTE_ADDR', $this->server)) {
      return $this->server['REMOTE_ADDR'];
    }

    return false;
  }

  private function getServerVariable($key) {
    if(array_key_exists($key, $this->server)) {
      return $this->server[$key];
    }
    return false;
  }

  private function loadFullUrl() {

    // base URL, no trailing slash eg: https://www.website.com
    $url = $this->isHttps() ? 'https://' : 'http://';
    $url .= $this->getServerVariable('HTTP_HOST');
    $this->baseUrl = $url;

    // full URL eg: https://www.website.com/ or https://www.website.com/page/1?hello=world
    $this->fullUrl = $url.$this->getUri();

    // check if request is cron request, have base cron url
    if(strpos($this->fullUrl, '/cron/') !== false) {
      $this->isCron = true;
    }

    // check if request is ajax request, have base ajax url
    if(strpos($this->fullUrl, '/ajax/') !== false) {
      $this->isAjax = true;
    }

    // check if request is audit request, have base audit url
    if(strpos($this->fullUrl, '/audit/') !== false) {
      $this->isAudit = true;
    }

    // uri without query parameters
    $this->uri = $this->getUri();

    // check if request is api request, have base api url
    if(strpos($this->fullUrl, '/api/') !== false) {
      $this->isApi = true;
      $this->uri = str_replace('/api/', '/', $this->getUri());
      return;
    }
  }

  private function arrayWalkRecursiveReferential(&$array, $function, $params = []) {
    $reference_function = function (&$value, $key, $data): void {
      $parameters = array_merge([$value], $data[1]);
      $value = call_user_func_array($data[0], $parameters);
    };
    array_walk_recursive($array, $reference_function, [$function, $params]);
  }

}