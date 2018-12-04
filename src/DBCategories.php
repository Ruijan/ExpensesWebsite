<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:34 PM
 */

namespace src;


class DBCategories
{
    private $database;
    private $driver;
    public function __construct($database){
        $this->driver = $database->getDriver();
        $this->database = $database;
        $query = "CREATE TABLE categories (
                        ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL,
                        PAYEE_ID int(11) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        PRIMARY KEY (ID))";
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't create table categories in ".$this->database->getDBName().". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function dropTable(){
        $query = "DROP TABLE categories";
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't drop table categories in ".$this->database->getDBName().". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }
}