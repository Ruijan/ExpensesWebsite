<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:08 PM
 */

namespace BackEnd\Database;
require_once ("DBTable.php");

class DBCurrencies extends DBTable
{
    public function __construct($database)
    {
        parent::__construct($database, "currencies");
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL UNIQUE,
                        SHORT_NAME char(5) NOT NULL,
                        CURRENT_DOLLARS_CHANGE int(11) NOT NULL,
                        PRIMARY KEY (ID)";
    }

    public function addCurrency($name, $shortName){
        $query = 'INSERT INTO '.$this->name.' (NAME, SHORT_NAME, CURRENT_DOLLARS_CHANGE) VALUES ("'.
            $this->driver->real_escape_string($name).'", "'.$this->driver->real_escape_string($shortName).'", 1)';
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't insert currency ".$name." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function getCurrencyFromID($currencyID){
        $query = "SELECT * FROM ".$this->getName()." WHERE ID = '".$this->driver->real_escape_string($currencyID)."'";
        $row = $this->driver->query($query)->fetch_assoc();
        return $row;
    }
}