<?php

namespace Service;

use Core\AutoLoader;
use Helper\File;

class Module {

  private $modules = [
    'modules' => [],
    'configs' => [],
    'routes' => [],
  ];

  public function load(AutoLoader $loader, $path) {
    $module_configs = [];
    $dirs = File::getDirList($path);
    foreach($dirs as $dir) {
      $module_file = $path.$dir.DS.'module.php';
      if(File::exists($module_file)) {
        $module = require_once($module_file);
        if(isset($module['enabled']) && $module['enabled'] == false) {
          continue;
        }

        $module['namespace'] = 'Module\\'.$dir.'\\';
        $module['dir'] = $path.$dir;

        $this->modules['modules'][$module['slug']] = [
          'name' => $module['name'],
          'slug' => $module['slug'],
          'namespace' => $module['namespace'],
          'dir' => $module['dir']
        ];

        $loader->addNamespace($module['namespace'], $path.$dir.DS.'src');

        // load configs
        $config_dir = $path.$dir.DS.'configs'.DS;
        if(File::dirExists($config_dir)) {
          $config_files = File::getFileList($config_dir, true);
          $configs = [];
          foreach($config_files as $config_file) {
            $include = require_once($config_file);
            $configs = array_merge($configs, $include);
          }
          $module_configs[$module['slug']] = $configs;
          foreach($module_configs as $module_config) {
            $this->modules['configs'] = array_replace_recursive($this->modules['configs'], $module_config);
          }
        }

        // load routes
        $route_dir = $path.$dir.DS.'routes'.DS;
        if(File::dirExists($route_dir)) {
          $route_files = File::getFileList($route_dir, true);
          $routes = [];
          foreach($route_files as $route_file) {
            $include = require_once($route_file);
            foreach($include as &$i) {
              $i['module'] = $module['slug'];
            }
            $routes = array_merge($routes, $include);
          }
          $this->modules['routes'] = array_merge(
            $this->modules['routes'],
            $routes
          );
        }
      }
    }
    return $this->modules;
  }

  public function getAll() {
    return $this->modules;
  }

  public function getModules() {
    return $this->modules['modules'];
  }

  public function getConfigs() {
    return $this->modules['configs'];
  }

  public function getRoutes() {
    return $this->modules['routes'];
  }

}