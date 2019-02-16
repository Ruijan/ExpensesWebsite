<?php
require_once 'vendor/autoload.php';
use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\Router;
use BackEnd\Database\DBTables;

$driver = new \mysqli("127.0.0.1", "root", "");
$dbName = "Expenses";
$tableFactory = new \BackEnd\Database\DBTableFactory();
$database = new \BackEnd\Database\Database($driver, $dbName);
$database->addTable($tableFactory->createTable(DBTables::Currencies, $database),DBTables::Currencies);
$database->addTable($tableFactory->createTable(DBTables::ExpenseStates, $database),DBTables::ExpenseStates);
$database->addTable($tableFactory->createTable(DBTables::Users, $database),DBTables::Users);
$database->addTable($tableFactory->createTable(DBTables::Categories, $database),DBTables::Categories);
$database->addTable($tableFactory->createTable(DBTables::SubCategories, $database),DBTables::SubCategories);
$database->addTable($tableFactory->createTable(DBTables::Accounts, $database),DBTables::Accounts);
$database->addTable($tableFactory->createTable(DBTables::Payees, $database),DBTables::Payees);
$database->addTable($tableFactory->createTable(DBTables::Expenses, $database),DBTables::Expenses);
$database->init();
$connectionRequestFactory = new ConnectionRequestFactory($database);
$router = new Router(new \BackEnd\Routing\ServerProperties(), ["connection" => $connectionRequestFactory]);
echo strlen($_SERVER['DOCUMENT_ROOT']);
echo $_SERVER['DOCUMENT_ROOT'].'<br/>';
$current_path = str_replace('\\', '/', substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT']),strlen(getcwd())));
$router->resolveRoute();
echo $router->getResponse();