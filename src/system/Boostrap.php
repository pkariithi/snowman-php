<?php

use Core\Container;
use Core\Router;
use Core\AutoLoader;
use Service\Module;
use Service\Config;
use Service\Session;
use Service\Request;
use Service\Response;
use Service\Logger;
use Service\Flash;

// autoloader
require SRC_DIR.'system'.DS.'Core'.DS.'AutoLoader.php';

// Basic namespace mapping
$loader = new AutoLoader();
$loader->register();
$loader->addNamespace("Base", SRC_DIR.'system'.DS.'Base');
$loader->addNamespace("Core", SRC_DIR.'system'.DS.'Core');
$loader->addNamespace("Exception", SRC_DIR.'system'.DS.'Exception');
$loader->addNamespace("Helper", SRC_DIR.'system'.DS.'Helper');
$loader->addNamespace("Service", SRC_DIR.'system'.DS.'Service');

// load modules - namespace, routes, config, migrations,
$module = new Module();
$module->load($loader, SRC_DIR.'modules'.DS);

// configs - merge global and module configs
$config = new Config();
$config->loadIni(SRC_DIR.'config'.DS.'ini.php', ENV);
$config->loadConfig(SRC_DIR.'config'.DS.'config.php', $module->getConfigs(), ENV);

// request
$request = new Request(
  $_GET,
  $_POST,
  $_COOKIE,
  $_FILES,
  $_SERVER,
  file_get_contents('php://input')
);

// session
$session = new Session();
$session->start($config->get('session.name'));

// response
$response = new Response();
$response->setBaseUrl($request->baseUrl);

// logger
$logger = new Logger($request, $config->get('log'));
$logger->separator();

// flash
$flash = new Flash($session, $response);

// container
$container = new Container();
$container->set("module", $module);
$container->set("config", $config);
$container->set("session", $session);
$container->set("request", $request);
$container->set("response", $response);
$container->set("logger", $logger);
$container->set("flash", $flash);

// router
$router = new Router($container);
$router->callActiveRoute();