<?php
require_once 'vendor/autoload.php';
use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\Router;
use BackEnd\Database\DBTables;
echo "Hellow world";
try{
    $driver = new \mysqli("127.0.0.1", "root", "");
    $dbName = "Expenses";
    $tableFactory = new \BackEnd\Database\DBTableFactory();
    $database = new \BackEnd\Database\Database($driver, $dbName);
    $tableNames = [DBTables::CURRENCIES, DBTables::EXPENSES_STATES, DBTables::USERS,
        DBTables::CATEGORIES, DBTables::SUBCATEGORIES, DBTables::ACCOUNTS,
        DBTables::PAYEES, DBTables::EXPENSES];
    foreach($tableNames as $tableName){
        $database->addTable($tableFactory->createTable($tableName, $database),$tableName);
    }
    $database->init();
    $connectionRequestFactory = new ConnectionRequestFactory($database);
    $router = new Router(new \BackEnd\Routing\ServerProperties(), ["connection" => $connectionRequestFactory]);
    $current_path = str_replace('\\', '/', substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT']),strlen(getcwd())));
    $router->resolveRoute();
    echo $router->getResponse();
    echo "Hellow world";
}catch(Exception $e){
    echo $e->getMessage();
}