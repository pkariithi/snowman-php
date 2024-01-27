<?php

namespace Service;

use Helper\Dot;
use Helper\File;

class Config {

  private $values = [];

  public function get(string $key, string $sep = '.') {
    $current = $this->values;
    $path = strtok($key, $sep);
    while ($path !== false) {
      if(!isset($current[$path])) {
        return null;
      }
      $current = $current[$path];
      $path = strtok($sep);
    }
    return $current;
  }

  public function loadIni(string $filepath, string $env) {
    if(File::exists($filepath)) {
      $ini = include_once($filepath);
      $ini_vars = array_replace_recursive($ini['base'], $ini['env'][$env]);
      foreach($ini_vars as $ini_name => $ini_value) {
        ini_set($ini_name, $ini_value);
      }
    }
  }

  public function loadConfig(string $filepath, array $module_configs, string $env) {
    if(File::exists($filepath)) {
      $config = include_once($filepath);
      $base_config = array_replace_recursive($config['base'], $config['env'][$env]);

      if(
        !isset($module_configs['base']) ||
        (isset($module_configs['base']) && empty($module_configs['base']))
      ) {
        $this->values = $base_config;
        return;
      }

      if(isset($module_configs['env'][$env]) && !empty($module_configs['env'][$env])) {
        $module_config = array_replace_recursive($module_configs['base'], $module_configs['env'][$env]);
        $this->values = array_replace_recursive($base_config, $module_config);
        return;
      }
    }
  }

}