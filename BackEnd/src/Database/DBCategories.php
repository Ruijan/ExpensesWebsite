<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:34 PM
 */

namespace BackEnd\Database;
require_once ("DBTable.php");
require_once("DBUsers.php");

class DBCategories extends DBTable
{
    private $usersTable;
    public function __construct($database, $usersTable){
        parent::__construct($database, "categories");
        $this->usersTable = $usersTable;
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL UNIQUE,
                        USER_ID int(11) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        PRIMARY KEY (ID)";
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    public function addCategory($category){
        if($this->usersTable->checkIfIDExists($category["USER_ID"]) == false){
            throw new \Exception("Couldn't insert category ".implode(" ,", $category)." in ".$this->name.
                ". Reason: User ID does not exist.");
        }
        $values = [];
        $indexValue = 0;
        foreach ($category as $value) {
            $values[$indexValue] = '"'.$this->driver->real_escape_string($value).'"';
            $indexValue += 1;
        }
        $values = implode(", ", $values);
        $query = 'INSERT INTO '.$this->driver->real_escape_string($this->name).
            ' (NAME, USER_ID, ADDED_DATE) VALUES ('.$values.')';
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't insert category ".implode(" ,", $category)." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function getCategoryFromID($id){
        $query = "SELECT * FROM ".$this->getName()." WHERE ID = '".$this->driver->real_escape_string($id)."'";
        if ($row = $this->driver->query($query)->fetch_assoc()) {
            return $row;
        }
    }
}