<?php
require_once 'vendor/autoload.php';
use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\Router;
use BackEnd\Database\DBTables;
echo "Hello world</br>";
try{
    $driver = new \mysqli("35.241.210.238", "root", "Jqc9ffuHBsbfcN68");
    $dbName = "Expenses";
    $tableFactory = new \BackEnd\Database\DBTableFactory();
    $database = new \BackEnd\Database\Database($driver, $dbName);
    $tableNames = [DBTables::CURRENCIES, DBTables::EXPENSES_STATES, DBTables::USERS,
        DBTables::CATEGORIES, DBTables::SUBCATEGORIES, DBTables::ACCOUNTS,
        DBTables::PAYEES, DBTables::EXPENSES];
    foreach($tableNames as $tableName){
        echo "Creating Database ".$tableName.".</br>";
        $database->addTable($tableFactory->createTable($tableName, $database),$tableName);
    }
    $database->init();
    $connectionRequestFactory = new ConnectionRequestFactory($database);
    $router = new Router(new \BackEnd\Routing\ServerProperties(), ["connection" => $connectionRequestFactory]);
    $current_path = str_replace('\\', '/', substr(getcwd(),strlen($_SERVER['DOCUMENT_ROOT']),strlen(getcwd())));
    $router->resolveRoute();
    echo $router->getResponse();
    echo "Hellow world</br>";
}catch(Exception $e){
    echo $e->getMessage();
}