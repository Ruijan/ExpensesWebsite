<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 9:42 AM
 */

namespace BackEnd\Routing\Request\Category;

use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\User;


class CategoryRequestFactory
{
    /** @var Database */
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function createRequest($type)
    {
        $postArray = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        switch ($type) {
            case "Create":
                return new CategoryCreation($this->database->getTableByName(DBTables::CATEGORIES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            case "RetrieveAll":
                return new RetrieveAllCategories($this->database->getTableByName(DBTables::CATEGORIES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            default:
                throw new \InvalidArgumentException("Request type: " . $type . " not found.");
        }
    }

    public function getDatabase()
    {
        return $this->database;
    }
}