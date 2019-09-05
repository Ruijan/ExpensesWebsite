<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:34 PM
 */

namespace BackEnd\Database\DBCategories;
use BackEnd\Category;
use BackEnd\Database\DBTable;
use BackEnd\Database\InsertionException;

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

    /**
     * @param Category $category
     * @throws InsertionException
     */
    public function addCategory($category){
        if($this->usersTable->doesUserIDExist($category->getUserID()) == false){
            throw new InsertionException("category", $category->asDict(), $this->name, "User ID does not exist.");
        }
        $query = 'INSERT INTO '.$this->driver->real_escape_string($this->name).
            ' (NAME, USER_ID, ADDED_DATE) VALUES ("'.$this->driver->real_escape_string($category->getName()).
            '", '.$this->driver->real_escape_string($category->getUserID()).
            ', "'.$this->driver->real_escape_string($category->getAddedDate()).'")';
        if ($this->driver->query($query) === FALSE) {
            throw new InsertionException("category", $category->asDict(), $this->name, $this->driver->error_list[0]["error"]);
        }
        $category->setID($this->driver->insert_id);
    }

    public function getCategoryFromID($categoryID){
        $query = "SELECT * FROM ".$this->getName()." WHERE ID = '".$this->driver->real_escape_string($categoryID)."'";
        $row = $this->driver->query($query)->fetch_assoc();
        return $row;
    }

    public function doesCategoryIDExist($userID){
        $query = "SELECT ID FROM " . $this->name . " WHERE ID = " . $this->driver->real_escape_string($userID);
        $result = $this->driver->query($query);
        return $result != false and  $result->num_rows != 0;
    }

    public function getAllCategories(){
        $query = "SELECT * FROM ".$this->getName();
        $result = $this->driver->query($query);
        $categories = [];
        while ($result and $row = $result->fetch_assoc()) {
            $category = new Category($row["NAME"], $row["USER_ID"], $row["ADDED_DATE"]);
            $category->setID($row["ID"]);
            $categories[] = $category;
        }
        return $categories;
    }

    public function checkIfIDExists($categoryID)
    {
        if (!$this->doesCategoryIDExist($categoryID)) {
            throw new UndefinedCategoryID($categoryID);
        }
    }

    /**
     * @param $categoryID
     * @throws UndefinedCategoryID
     */
    public function deleteCategory($categoryID)
    {
        $this->checkIfIDExists($categoryID);
        $query = "DELETE FROM " . $this->name . " WHERE ID='" . $this->driver->real_escape_string($categoryID) . "'";
        $this->driver->query($query);
    }
}