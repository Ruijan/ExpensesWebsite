<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 8:05 PM
 */

namespace src;


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
            throw new \Exception("Couldn't create table ".$this->name." in ".$this->database->getDBName().". Reason: ".$this->driver->error_list[0]["error"]);
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
            throw new \Exception("Couldn't drop table ".$this->name." in ".$this->database->getDBName().". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }
}