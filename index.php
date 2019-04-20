<?php
require_once 'vendor/autoload.php';
use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\Router;
use BackEnd\Database\DBTables;

/**
 * @param $db
 * @return array
 */
function getDBParameters(): array
{
    $server = "127.0.0.1";
    $username = "root";
    $password = "";
    $db = "expenses";
    $prodDBParams = getenv("CLEARDB_DATABASE_URL");
    if ($prodDBParams !== false){
        $prodDBParams = ($prodDBParams);
        $server = $prodDBParams["host"];
        $username = $prodDBParams["user"];
        $password = $prodDBParams["pass"];
        $db = substr($prodDBParams["path"], 1);
    }

    return array(
        'dsn' => '',
        'hostname' => $server,
        'username' => $username,
        'password' => $password,
        'database' => $db,
        'dbdriver' => 'mysqli',
        'dbprefix' => '',
        'pconnect' => FALSE,
        'cache_on' => FALSE,
        'cachedir' => '',
        'char_set' => 'utf8',
        'dbcollat' => 'utf8_general_ci',
        'swap_pre' => '',
        'encrypt' => FALSE,
        'compress' => FALSE,
        'stricton' => FALSE,
        'failover' => array(),
        'save_queries' => TRUE
    );
}

try{
    $dbParams = getDBParameters();
    $driver = new \mysqli($dbParams["hostname"], $dbParams["username"], $dbParams["password"]);
    $dbName = $dbParams["database"];
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
    echo "Hellow world</br>";
}catch(Exception $e){
    echo $e->getMessage();
}