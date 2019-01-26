<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/25/2019
 * Time: 9:25 PM
 */

namespace BackEnd;


class Account
{
    private $name;
    private $currentAmount;
    private $tableID;
    private $expenses = [];

    public function __construct($name, $tableID, $currentAmount = 0)
    {
        $this->currentAmount = $currentAmount;
        $this->name = $name;
        $this->tableID = $tableID;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCurrentAmount()
    {
        return $this->currentAmount;
    }

    public function getTableID()
    {
        return $this->tableID;
    }

    public function asDict()
    {
        return ["NAME" => $this->name, "ID" => $this->tableID,
            "CURRENT_AMOUNT" => $this->currentAmount];
    }

    public function loadExpenses($expensesTable){
        $this->expenses = $expensesTable->getExpensesForAccountID($this->tableID);
    }

    public function getExpenses(){
        return $this->expenses;
    }
}