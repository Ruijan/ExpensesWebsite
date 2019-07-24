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
use BackEnd\Routing\Request\RequestFactory;
use BackEnd\User;

class ExpenseStateRequestFactory extends RequestFactory
{
    public function createRequest($type, $data)
    {
        switch ($type) {
            case "Create":
                return new ExpenseStateCreation($this->database->getTableByName(DBTables::EXPENSES_STATES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "RetrieveAll":
                return new RetrieveAllExpenseStates($this->database->getTableByName(DBTables::EXPENSES_STATES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "Delete":
                return new DeleteExpenseState($this->database->getTableByName(DBTables::EXPENSES_STATES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            default:
                throw new \InvalidArgumentException("Request type: " . $type . " not found.");
        }
    }
}