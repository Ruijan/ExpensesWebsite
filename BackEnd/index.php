<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();

require_once '../vendor/autoload.php';
use BackEnd\Application;

$app = new Application();
$app->start();