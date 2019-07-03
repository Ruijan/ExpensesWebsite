<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:14 PM
 */

namespace BackEnd\Database\DBSubCategories;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBTable;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Database\InsertionException;
use BackEnd\SubCategory;

class DBSubCategories extends DBTable
{
    /** @var DBUsers */
    private $dbPayers;
    /** @var DBCategories */
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
        if($this->dbPayers->doesUserIDExist($subCategory->getUserID()) == false){
            throw new InsertionException("SubCategory",
                $subCategory->asDict(), $this->name, "Payer ID does not exist.");
        }
        if($this->dbCategories->doesCategoryIDExist($subCategory->getParentID()) == false){
            throw new InsertionException("SubCategory",
                $subCategory->asDict(), $this->name, "Parent Category ID does not exist.");
        }
        $query = 'INSERT INTO '.$this->driver->real_escape_string($this->name).
            ' (PARENT_ID, NAME, USER_ID, ADDED_DATE) VALUES ('.$this->driver->real_escape_string($subCategory->getParentID()).
            ', "'.$this->driver->real_escape_string($subCategory->getName()).
            '", '.$this->driver->real_escape_string($subCategory->getUserID()).
            ', "'.$this->driver->real_escape_string($subCategory->getAddedDate()).'")';
        if ($this->driver->query($query) === FALSE) {
            throw new InsertionException("SubCategory", $subCategory->asDict(), $this->name, $this->driver->error_list[0]["error"]);
        }
    }

    public function getSubCategoryFromID($subCategoryID){
        $query = "SELECT * FROM ".$this->getName()." WHERE ID = '".$this->driver->real_escape_string($subCategoryID)."'";
        $row = $this->driver->query($query)->fetch_assoc();
        return $row;
    }

    public function getAllSubCategories(){
        $query = "SELECT * FROM ".$this->getName();
        $result = $this->driver->query($query);
        $categories = [];
        while ($result and $row = $result->fetch_assoc()) {
            $subCategory = new SubCategory($row["NAME"], $row["USER_ID"], $row["PARENT_ID"], $row["ADDED_DATE"]);
            $subCategory->setID($row["ID"]);
            $categories[] = $subCategory;
        }
        return $categories;
    }
}