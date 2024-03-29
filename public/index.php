<?php
require '../vendor/autoload.php';

use NewK\routes\Requests;
use Slim\App;


$app = new App(['debug' => true]);

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);
// Set up dependencies
$dependencies = require __DIR__ . '/../src/dependencies.php';
$dependencies($app);
// Register middleware
$middleware = require __DIR__ . '/../src/middleware.php';
$middleware($app);
// Register routes
$routes = require __DIR__ . '/../src/routes.php';
$routes($app);

$app->run();