<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/24/2019
 * Time: 8:10 PM
 */

namespace BackEnd\Routing\Request\Expense;
use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\RequestFactory;
use BackEnd\User;

class ExpenseRequestFactory extends RequestFactory
{
    public function createRequest($type, $data)
    {
        switch ($type) {
            case "Create":
                return new ExpenseCreation($this->database->getTableByName(DBTables::EXPENSES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "RetrieveAllFromAccount":
                return new RetrieveAllExpenses($this->database->getTableByName(DBTables::EXPENSES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "Delete":
                return new DeleteExpense($this->database->getTableByName(DBTables::EXPENSES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            default:
                throw new \InvalidArgumentException("Request type: " . $type . " not found.");
        }
    }
}