<?php
require_once 'vendor/autoload.php';
use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\Request\Router;

$router = new Router(new \BackEnd\Routing\ServerProperties());
$router->resolveRoute();