<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 8:05 PM
 */

namespace BackEnd\Database;
use Throwable;

class TableCreationException extends \Exception{
    public function __construct(string $tableName, string $databaseName, string $error, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't create table ".$tableName." in ".$databaseName.". Reason: ".$error, $code, $previous);
    }
}

class TableDropException extends \Exception{
    public function __construct(string $tableName, string $databaseName, string $error, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Couldn't drop table ".$tableName." in ".$databaseName.". Reason: ".$error, $code, $previous);
    }
}

class DBTable
{
    protected $name;
    protected $database;
    protected $driver;
    public function __construct($database, $name)
    {
        $this->database = $database;
        $this->driver = $database->getDriver();
        $this->name = $name;
    }

    public function init(){
        $query = "CREATE TABLE ".$this->name." (".$this->getTableHeader().")";
        if ($this->driver->query($query) === FALSE) {
            throw new TableCreationException($this->name, $this->database->getDBName(), $this->driver->error_list[0]["error"]);
        }
    }

    public function getDatabase(){
        return $this->database;
    }

    public function getDriver(){
        return $this->driver;
    }
    public function getName(){
        return $this->name;
    }

    public function getTableHeader(){
        return "ID int(11) AUTO_INCREMENT UNIQUE";
    }

    public function dropTable(){
        $query = "DROP TABLE ".$this->name;
        if ($this->driver->query($query) === FALSE) {
            throw new TableDropException($this->name, $this->database->getDBName(), $this->driver->error_list[0]["error"]);
        }
    }
}