<?php
require_once 'vendor/autoload.php';
use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\Router;
use BackEnd\Database\DBTables;

$driver = new \mysqli("127.0.0.1", "root", "");
$dbName = "Expenses";
$tableFactory = new \BackEnd\Database\DBTableFactory();
$database = new \BackEnd\Database\Database($driver, $dbName);
$database->addTable($tableFactory->createTable(DBTables::CURRENCIES, $database),DBTables::CURRENCIES);
$database->addTable($tableFactory->createTable(DBTables::EXPENSES_STATES, $database),DBTables::EXPENSES_STATES);
$database->addTable($tableFactory->createTable(DBTables::USERS, $database),DBTables::USERS);
$database->addTable($tableFactory->createTable(DBTables::CATEGORIES, $database),DBTables::CATEGORIES);
$database->addTable($tableFactory->createTable(DBTables::SUBCATEGORIES, $database),DBTables::SUBCATEGORIES);
$database->addTable($tableFactory->createTable(DBTables::ACCOUNTS, $database),DBTables::ACCOUNTS);
$database->addTable($tableFactory->createTable(DBTables::PAYEES, $database),DBTables::PAYEES);
$database->addTable($tableFactory->createTable(DBTables::EXPENSES, $database),DBTables::EXPENSES);
$database->init();
$connectionRequestFactory = new ConnectionRequestFactory($database);
$router = new Router(new \BackEnd\Routing\ServerProperties(), ["connection" => $connectionRequestFactory]);
$current_path = str_replace('\\', '/', substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT']),strlen(getcwd())));
$router->resolveRoute();
echo $router->getResponse();