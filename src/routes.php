<?php

use Slim\App;
use NewK\routes\Requests;

return function (App $app) {
//    $container = $app->getContainer();
    $app->group("/request", function (App $app) {
        $app->post("", Requests::class . ":NewRequest");
        $app->put("/{id}", Requests::class . ":UpdateRequest");
        $app->get("", Requests::class . ":ListRequests");
    });
};
