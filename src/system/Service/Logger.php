<?php

namespace Service;

use Service\Request;
use Helper\File;
use Helper\Date;

class Logger {

  private $request;
  private $config; // log config
  private $logSession;
  private $sep = '-';

  public function __construct(Request $request, array $config) {
    $this->request = $request;
    $this->config = $config;
  }

  public function setLogSession($logSession) {
    $this->logSession = $logSession;
  }

  public function debug($file, $line, $controller, $message = '', $params = []) {
    $this->write('debug', $file, $line, $controller, $message, $params);
  }

  public function info($file, $line, $controller, $message = '', $params = []) {
    $this->write('info', $file, $line, $controller, $message, $params);
  }

  public function warn($file, $line, $controller, $message = '', $params = []) {
    $this->write('warn', $file, $line, $controller, $message, $params);
  }

  public function error($file, $line, $controller, $message = '', $params = []) {
    $this->write('error', $file, $line, $controller, $message, $params);
  }

  public function separator() {
    $this->write('sep', null, null, null, null, null);
  }

  private function mergeVars($vars) {

    // http vars
    $method = $this->request->getMethod();

    // defaults - in the order they will be written in the log file
    $defaults = [
      'LogSession' => $this->logSession,
      'RequestURL' => $this->request->fullUrl,
      'HTTPMethod' => $method,
      'Controller' => null,
      'File' => null,
      'Message' => null,
    ];

    // merge
    $merged = [];
    foreach($defaults as $k => $v) {
      $value = $v;
      if(isset($vars[$k])) {
        $value = $vars[$k];
        unset($vars[$k]);
      }

      if(empty($value)) {
        continue;
      }

      switch($k) {
        case 'File':
          $value = str_replace([ROOT], '', $value);
          break;
        case 'Message':
        case 'Controller':
          $value = str_replace(
            ['\Controller\\','\Core\\','\Model\\',ROOT],
            '',
            $value
          );
          break;
        case 'RequestURL':
          $value = str_replace($this->request->baseUrl, '', $value);
          break;
      }

      $merged[$k] = $value;
    }
    return array_merge($merged, $vars);
  }

  private function write($type, $file, $line, $controller, $message, $params) {
    // logging is disabled
    if(!$this->config['enabled']) {
      return;
    }

    // don't log ajax requests
    if($this->request->isAjax) {
      return;
    }

    // check level
    if($type != 'sep') {
      $levels = array_flip($this->config['levels']);
      if($levels[$type] < $levels[$this->config['level']]) {
        return;
      }
    }

    // logfile
    $today = Date::now('Y-m-d');
    $logfile = $this->config['path'].$today.'.log';

    if($type !== 'sep') {

      // merge vars
      $vars = $this->mergeVars(
        array_merge($params, [
          'File' => $file.':'.$line,
          'Controller' => $controller,
          'Message' => $message
        ])
      );

      // log options
      $pad_len = 5;
      $date = Date::now('Y-m-d H-i-s.u');

      // type
      $type = str_pad(mb_strtoupper($type), $pad_len, ' ', STR_PAD_RIGHT);

      // log vars
      $varsStr = "| {$type} ";
      foreach($vars as $key => $val) {
        $varsStr .= "| {$key}={$val} ";
      }
      $varsStr = trim($varsStr);

      // log message
      $text = "{$date} {$varsStr}\n";

    } else {
      $text = "\n".str_repeat($this->sep, 50)."\n\n";
    }

    // write to file
    File::writeFileContents($text, $logfile, true, true);
  }

}