<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:14 PM
 */

namespace BackEnd\Database;
require_once ("DBTable.php");

class DBSubCategories extends DBTable
{
    private $dbPayers;
    private $dbCategories;
    public function __construct($database, $dbPayers, $dbCategories){
        parent::__construct($database, "sub_categories");
        $this->dbPayers = $dbPayers;
        $this->dbCategories = $dbCategories;
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        PARENT_ID int(11) NOT NULL,
                        NAME char(50) NOT NULL UNIQUE,
                        USER_ID int(11) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        PRIMARY KEY (ID)";
    }

    public function getDBPayers(){
        return $this->dbPayers;
    }

    public function getDBCategories(){
        return $this->dbCategories;
    }

    public function addSubCategory($subCategory){
        if($this->dbPayers->checkIfIDExists($subCategory["USER_ID"]) == false){
            throw new \Exception("Couldn't insert sub category ".implode(" ,", $subCategory)." in ".$this->name.
                ". Reason: Payer ID does not exist.");
        }
        if($this->dbCategories->checkIfCategoryIDExists($subCategory["PARENT_ID"]) == false){
            throw new \Exception("Couldn't insert sub category ".implode(" ,", $subCategory)." in ".$this->name.
                ". Reason: Parent Category ID does not exist.");
        }
        $values = [];
        $indexValue = 0;
        foreach ($subCategory as $value) {
            $values[$indexValue] = '"'.$this->driver->real_escape_string($value).'"';
            $indexValue += 1;
        }
        $values = implode(", ", $values);
        $query = 'INSERT INTO '.$this->driver->real_escape_string($this->name).
            ' (PARENT_ID, NAME, USER_ID, ADDED_DATE) VALUES ('.$values.')';
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't insert sub category ".implode(", ", $subCategory)."in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function getSubCategoryFromID($subCategoryID){
        $query = "SELECT * FROM ".$this->getName()." WHERE ID = '".$this->driver->real_escape_string($subCategoryID)."'";
        $row = $this->driver->query($query)->fetch_assoc();
        return $row;
    }
}