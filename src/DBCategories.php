<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:34 PM
 */

namespace src;
require_once ("DBTable.php");
require_once ("DBPayer.php");

class DBCategories extends DBTable
{
    private $dbPayers;
    public function __construct($database, $dbPayers){
        parent::__construct($database, "categories");
        $this->dbPayers = $dbPayers;
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL UNIQUE,
                        PAYER_ID int(11) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        PRIMARY KEY (ID)";
    }

    public function getDBPayers(){
        return $this->dbPayers;
    }

    public function addCategory($category){
        if($this->dbPayers->checkIfPayerIDExists($category["PAYER_ID"]) !== $category["PAYER_ID"]){
            throw new \Exception("Couldn't insert category ".implode(" ,", $category)." in ".$this->name.
                ". Reason: Payer ID does not exist.");
        }
        $values = [];
        $indexValue = 0;
        foreach ($category as $value) {
            $values[$indexValue] = '"'.$this->driver->real_escape_string($value).'"';
            $indexValue += 1;
        }
        $values = implode(", ", $values);
        $query = 'INSERT INTO '.$this->driver->real_escape_string($this->name).
            ' (NAME, PAYER_ID, ADDED_DATE) VALUES ('.$values.')';
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't insert category ".implode(" ,", $category)." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }
}