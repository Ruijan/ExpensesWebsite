<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 9:07 PM
 */

namespace BackEnd\Routing\Request\ExpenseState;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\User;

class ExpenseStateRequestFactory
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
                return new ExpenseStateCreation($this->database->getTableByName(DBTables::EXPENSES_STATES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            case "RetrieveAll":
                return new RetrieveAllExpenseStates($this->database->getTableByName(DBTables::EXPENSES_STATES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            case "Delete":
                return new DeleteExpenseState($this->database->getTableByName(DBTables::EXPENSES_STATES), $postArray);
            default:
                throw new \InvalidArgumentException("Request type: " . $type . " not found.");
        }
    }

    public function getDatabase()
    {
        return $this->database;
    }
}