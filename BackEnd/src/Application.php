<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/8/2019
 * Time: 4:47 PM
 */

namespace BackEnd;
use BackEnd\Database\DBTableFactory;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\Category\SubCategoryRequestFactory;
use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;
use BackEnd\Routing\Request\Account\AccountRequestFactory;
use BackEnd\Routing\Request\Currency\CurrencyRequestFactory;
use BackEnd\Routing\ServerProperties;
use BackEnd\Routing\Router;

class Application
{
    /** @var Database */
    private $database;
    /** @var Router */
    private $router;

    public function __construct(){
        $this->initDatabase();
        $this->initRouter();
    }

    public function start(){
        $this->router->resolveRoute();
        echo $this->router->getResponse();
    }

    public function getDatabase(): Database{
        return $this->database;
    }

    private function initDatabase(){
        $dbParams = $this->getDBParameters();
        $driver = new \mysqli($dbParams["hostname"], $dbParams["username"], $dbParams["password"]);
        $this->database = new Database($driver, $dbParams["database"]);
        $tableFactory = new DBTableFactory();
        $tableNames = [DBTables::CURRENCIES, DBTables::EXPENSES_STATES, DBTables::USERS,
            DBTables::CATEGORIES, DBTables::SUBCATEGORIES, DBTables::ACCOUNTS,
            DBTables::PAYEES, DBTables::EXPENSES];
        foreach($tableNames as $tableName){
            $this->database->addTable($tableFactory->createTable($tableName, $this->database),$tableName);
        }
        $this->database->init();
    }

    private function initRouter(){
        $connectionRequestFactory = new ConnectionRequestFactory($this->database);
        $accountRequestFactory = new AccountRequestFactory($this->database);
        $currencyRequestFactory = new CurrencyRequestFactory($this->database);
        $categoryRequestFactory = new SubCategoryRequestFactory($this->database);
        $this->router = new Router(new ServerProperties(),
            ["connection" => $connectionRequestFactory,
                "account" => $accountRequestFactory,
                "currency" => $currencyRequestFactory,
                "category" => $categoryRequestFactory]);
    }

    private function getDBParameters(): array{
        $server = "127.0.0.1";
        $username = "root";
        $password = "";
        $databaseName = "expenses";
        $prodDBParams = getenv("CLEARDB_DATABASE_URL");
        if ($prodDBParams !== false){
            $prodDBParams = parse_url($prodDBParams);
            $server = $prodDBParams["host"];
            $username = $prodDBParams["user"];
            $password = $prodDBParams["pass"];
            $databaseName = substr($prodDBParams["path"], 1);
        }

        return array(
            'dsn' => '',
            'hostname' => $server,
            'username' => $username,
            'password' => $password,
            'database' => $databaseName,
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
}