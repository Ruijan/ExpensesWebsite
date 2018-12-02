<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 5:17 PM
 */

namespace src;


class DBExpenses
{
    private $database;
    private $driver;
    public function __construct($database){
        $this->driver = $database->getDriver();
        $this->database = $database;
        $query = "CREATE TABLE expenses (
                        ID int(11) AUTO_INCREMENT UNIQUE,
                        LOCATION char(50) NOT NULL,
                        PAYEE_ID int(11) NOT NULL,
                        CATEGORY_ID int(1) NOT NULL,
                        SUB_CATEGORY_ID int(1) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        EXPENSE_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        AMOUNT double NULL,
                        CURRENCY_ID int(1) NOT NULL,
                        STATE int NULL,
                        PRIMARY KEY  (ID))";
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't create table expenses in ".$this->database->getDBName().". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function dropTable(){
        $query = "DROP TABLE expenses";
        $this->driver->query($query);
    }
}