<?php

namespace Core;

use Core\Container;
use Helper\File;

class Router {

  private $container;
  private $module;
  private $request;
  private $logger;
  private $routes;
  private $activeRoute;
  private $genericErrorMessage = "Internal Server Error. Kindly try again later";

  public function __construct(Container $container) {
    $this->container = $container;
    $this->request = $container->get('request');
    $this->logger = $container->get('logger');
    $this->module = $container->get('module');
    $this->routes = $this->module->getRoutes();

    $this->formatRoutes();
    $this->parseRoutes();
    $this->activeRoute = $this->getActiveRoute();
    $container->set('activeRoute', $this->activeRoute);
  }

  public function callActiveRoute() {

    // is route disabled
    if(!$this->activeRoute['enabled']) {
      die("Route {$this->activeRoute['name']} is disabled");
    }

    // route parts
    $module = $this->module->getModules()[$this->activeRoute['module']];
    $routeController = explode('@', $this->activeRoute['controller']);
    $controller = $routeController[0];
    $method = $routeController[1];

    // load controller
    $controllerFile = $module['dir'].DS.'src'.DS.'Controller'.DS.$controller.'.php';
    if(File::exists($controllerFile)) {
      $controller = $module['namespace'].'Controller\\'.$controller;
      $controllerObj = new $controller($this->container);

      // check controller is subclass of basecontroller
      if(is_subclass_of($controllerObj, '\\Base\\Controller')) {
        if(method_exists($controllerObj, $method) && is_callable([$controllerObj, $method])) {
          return $controllerObj->{$method}();
        } else {
          die("Method '{$method}' not found or not callable.");
        }

      } else {
        die("Controller '{$controller}' found but is not subclass of Base\Controller");
      }

    } else {
      die("Controller file '{$controllerFile}' not found.");
    }
  }

  private function formatRoutes() {
    $default = [
      'enabled' => true, // all routes enabled by default
      'isProtected' => true, // all routes protected by default
      // 'isAjax' => false,
      // 'isAudit' => false,
      // 'isCron' => false,
      // 'isApi' => false,
      'params' => []
    ];
    foreach($this->routes as &$r) {
      foreach($default as $dk => $dv) {
        if(!isset($r[$dk])) {
          $r[$dk] = $dv;
        }
      }
    }
  }

  private function parseRoutes() {
    $parsedRoutes = [];
    foreach($this->routes as $route) {
      if(strpos($route['url'], '{') !== false) {
        $routeParts = explode('{', rtrim($route['url'], '}'));

        $urlRoutes = [];
        foreach($routeParts as $k => $routePart) {
          if($k == 0) {
            $urlRoutes[$k] = $routePart;
          } else {
            $n = str_replace([':','/'], '', $routePart);
            $urlRoutes[$k.'_'.$n] = $routeParts[0].$routePart;
          }
        }
        foreach($urlRoutes as $urVal) {
          $params = [];
          foreach($route['params'] as $paramKey => $paramVal) {
            if(strpos($urVal, $paramKey) !== false) {
              $params[$paramKey] = $paramVal;
            }
          }

          $parsedRoutes[] = array_merge($route, [
            'url' => $urVal,
            'params' => $params
          ]);
        }

      } else {
        $parsedRoutes[] = $route;
      }
    }

    $this->routes = $parsedRoutes;
  }

  private function getActiveRoute() {
    // has route been found
    $found = false;

    // get direct active route
    foreach($this->routes as $k => $routeDetails) {
      if(
        $routeDetails['enabled'] &&
        $this->request->uri === $routeDetails['url'] &&
        in_array($this->request->getMethod(), (array) $routeDetails['methods'])
      ) {
        $route = $this->routes[$k];
        $route['values'] = [];
        $found = true;
        break;
      }
    }

    // get route with variables. matches urls like /user/1 to /user/{userId}
    // the idea is to match the parameters
    if(!$found):

      // 1. get all routes with params
      $paramRoutes = [];
      foreach($this->routes as $k => $routeDetails) {
        if(strpos($routeDetails['url'], ':')) {
          $paramRoutes[$k] = $routeDetails;
        }
      }

      // 2. count number of params
      $uriArr = explode('/', $this->request->uri);
      $uriCount = count($uriArr);

      // 3. loop through the routes with params checking param count
      foreach($paramRoutes as $j => $prRoute) {

        $prRouteArr = explode('/', $prRoute['url']);
        $prRouteCount = count($prRouteArr);

        // if counts match, we are a step closer to get the active route
        if($uriCount == $prRouteCount):

          // 4. get differences between uri and url (the params)
          // example: /hello/:id/world and /hello/1/world will return ':id = 1' as the diff
          $diff = array_combine(
            array_diff_assoc($prRouteArr, $uriArr),
            array_diff_assoc($uriArr, $prRouteArr)
          );
          $diffCount = count($diff);

          // 5. check if number of route params equals the diffCount
          if($diffCount == count((array) $prRoute['params'])) {

            // 6. check param regex
            $paramsMatch = [];
            foreach($diff as $diffParam => $diffValue) {
              $diffParam = str_replace(':', '', $diffParam);
              if(preg_match('/^'.$prRoute['params'][$diffParam].'$/', $diffValue)) {
                $paramsMatch[$diffParam] = $diffValue;
              }
            }

            // if all params matched
            if(count($paramsMatch) == $diffCount) {

              // 7. check method
              if(in_array($this->request->getMethod(), (array) $prRoute['methods'])) {
                $route = $prRoute;
                $route['values'] = $paramsMatch;

                $found = true;
              }

            }
          }
        endif;
      }
    endif;

    if(!$found) {
      $this->logger->error(__FILE__,__LINE__,__METHOD__,"Route for {$this->request->uri} not found.");
      $e404Route = $this->getRouteByModuleAndName('error', '404');
      if($e404Route) {
        $this->logger->error(__FILE__,__LINE__,__METHOD__,"Returning 404 route.");
        return $e404Route;
      }

      $this->logger->error(__FILE__,__LINE__,__METHOD__,"404 route not found. Returning generic error message '{$this->genericErrorMessage}'");
      die($this->genericErrorMessage);
    }

    $this->logger->info(__FILE__,__LINE__,__METHOD__,"Route for {$this->request->uri} found.");
    return $route;
  }

  private function getRouteByModuleAndName(string $module, string $name) {
    $found = array_filter($this->routes, function($route) use ($module, $name) {
      return ($route['module'] == $module && $route['name'] == $name);
    });
    return array_shift($found);
  }
}