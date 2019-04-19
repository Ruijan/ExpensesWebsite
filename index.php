<?php
require_once 'vendor/autoload.php';
use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\Router;
use BackEnd\Database\DBTables;

try{
    echo "Retrieving db infos<br/>";
    //Get Heroku ClearDB connection information
    $cleardb_url      = parse_url(getenv("CLEARDB_DATABASE_URL"));
    $cleardb_server   = $cleardb_url["host"];
    $cleardb_username = $cleardb_url["user"];
    $cleardb_password = $cleardb_url["pass"];
    $cleardb_db       = substr($cleardb_url["path"],1);

    echo $cleardb_db;
    $active_group = 'default';
    $query_builder = TRUE;

    $db['default'] = array(
        'dsn'    => '',
        'hostname' => $cleardb_server,
        'username' => $cleardb_username,
        'password' => $cleardb_password,
        'database' => $cleardb_db,
        'dbdriver' => 'mysqli',
        'dbprefix' => '',
        'pconnect' => FALSE,
        'db_debug' => (ENVIRONMENT !== 'production'),
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
    echo "Connecting to DB...";
    $driver = new \mysqli($cleardb_server, $cleardb_username, $cleardb_password);
    echo "OK<br/>";
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