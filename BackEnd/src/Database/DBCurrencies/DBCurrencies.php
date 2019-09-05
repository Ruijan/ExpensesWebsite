<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:08 PM
 */

namespace BackEnd\Database\DBCurrencies;

use BackEnd\Database\InsertionException;
use BackEnd\Database\DBTable;

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
                        CURRENT_DOLLARS_CHANGE float(24) NOT NULL,
                        PRIMARY KEY (ID)";
    }

    public function addCurrency($name, $shortName){
        $query = 'INSERT INTO '.$this->name.' (NAME, SHORT_NAME, CURRENT_DOLLARS_CHANGE) VALUES ("'.
            $this->driver->real_escape_string($name).'", "'.$this->driver->real_escape_string($shortName).'", 1)';
        if ($this->driver->query($query) === FALSE) {
            throw new InsertionException("currency",[$name],$this->name,$this->driver->error_list[0]["error"]);
        }
        return $this->driver->insert_id;
    }

    public function getCurrencyFromID($currencyID){
        $query = "SELECT * FROM ".$this->getName()." WHERE ID = '".$this->driver->real_escape_string($currencyID)."'";
        $row = $this->driver->query($query)->fetch_assoc();
        return $row;
    }

    public function doesCurrencyIDExist($currencyID){
        $query = "SELECT ID FROM " . $this->getName() . " WHERE ID = " . $this->driver->real_escape_string($currencyID);
        $result = $this->driver->query($query);
        return $result->num_rows != 0;
    }

    public function doesCurrencyExist($name, $shortName){
        $query = "SELECT ID FROM " . $this->getName() .
            " WHERE NAME = '" . $this->driver->real_escape_string($name).
            "' AND SHORT_NAME='".$this->driver->real_escape_string($shortName)."'";
        $result = $this->driver->query($query);
        return $result->num_rows != 0;
    }

    public function getAllCurrencies(){
        $query = "SELECT * FROM ".$this->getName();
        $result = $this->driver->query($query);
        $currencies = [];
        while ($result and $row = $result->fetch_assoc()) {
            $currencies[] = $row;
        }
        return $currencies;
    }

    public function deleteCurrency($name, $shortName){
        if(!$this->doesCurrencyExist($name, $shortName)){
            throw new UndefinedCurrencyException($name, $shortName);
        }
        $query = "DELETE FROM " . $this->name . " WHERE NAME='" . $this->driver->real_escape_string($name) .
            "' AND SHORT_NAME='".$this->driver->real_escape_string($shortName)."'";
        $this->driver->query($query);
    }
}