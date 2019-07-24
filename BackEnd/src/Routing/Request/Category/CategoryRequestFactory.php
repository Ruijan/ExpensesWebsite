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
use BackEnd\Routing\Request\RequestFactory;
use BackEnd\User;


class CategoryRequestFactory extends RequestFactory
{
    public function createRequest($type, $data)
    {
        switch ($type) {
            case "Create":
                return new CategoryCreation($this->database->getTableByName(DBTables::CATEGORIES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "RetrieveAll":
                return new RetrieveAllCategories($this->database->getTableByName(DBTables::CATEGORIES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            default:
                throw new \InvalidArgumentException("Request type: " . $type . " not found.");
        }
    }
}