<?php 
use \Psr\Http\Message\ServerRequestInterface as Request; 
use \Psr\Http\Message\ResponseInterface as Response;  

require '../vendor/autoload.php';
require '../src/config/db.php';  
$app = new \Slim\App; 

$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});
require '../src/routes/notifications.php'; 
require '../src/routes/buildings.php'; 
require '../src/routes/novedades.php'; 

$app->run();
