<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 5:17 PM
 */

namespace BackEnd\Database\DBExpenses;
use BackEnd\Database\DBTables;
use mysql_xdevapi\Exception;

use BackEnd\Database\DBTable;
use BackEnd\Expense;
use BackEnd\Database\DBExpenses\WrongTypeKeyException;
use BackEnd\Database\DBExpenses\InsertionKeyException;
use BackEnd\Database\DBExpenses\InsertionException;

class DBExpenses extends DBTable
{
    private $header = ["ID" => "integer",
        "LOCATION" => "string",
        "ACCOUNT_ID" => "integer",
        "PAYEE_ID" => "integer",
        "CATEGORY_ID" => "integer",
        "SUB_CATEGORY_ID" => "integer",
        "EXPENSE_DATE" => "string",
        "AMOUNT" => "double",
        "CURRENCY_ID" => "integer",
        "STATE_ID" => "integer"];
    private $categoriesTable;
    private $subCategoriesTable;
    private $payeesTable;
    private $currenciesTable;
    private $statesTable;
    public function __construct($database){
        parent::__construct($database, "expenses");
        $this->categoriesTable = $database->getTableByName(DBTables::Categories);
        $this->subCategoriesTable = $database->getTableByName(DBTables::SubCategories);
        $this->payeesTable = $database->getTableByName(DBTables::Payees);
        $this->currenciesTable = $database->getTableByName(DBTables::Currencies);
        $this->statesTable = $database->getTableByName(DBTables::ExpenseStates);
    }

    public function getTableHeader(){
        return "ID int(11) AUTO_INCREMENT UNIQUE,
            LOCATION char(50) NOT NULL,
            ACCOUNT_ID int(11) NOT NULL,
            PAYEE_ID int(11) NOT NULL,
            CATEGORY_ID int(11) NOT NULL,
            SUB_CATEGORY_ID int(11) NOT NULL,
            EXPENSE_DATE datetime DEFAULT '2018-01-01 00:00:00',
            AMOUNT double NULL,
            CURRENCY_ID int(11) NOT NULL,
            STATE_ID int NULL,
            PRIMARY KEY  (ID)";
    }

    public function addExpense($expense){
        $query = $this->getInsertExpenseQuery($expense);
        $this->tryAddingExpense($query);
    }

    protected function getInsertExpenseQuery($expense): string
    {
        $insertHeader = array_slice($this->header, 1);
        $properties = $this->getSQLValuesToInsert($expense, $insertHeader);
        $properties = implode(", ", $properties);
        $header = implode(", ", array_keys ($insertHeader));
        $query = 'INSERT INTO ' . $this->name . ' (' . $header . ') VALUES (' . $properties . ')';
        return $query;
    }

    protected function tryAddingExpense(string $query): void
    {
        $resultQuery = $this->driver->query($query);
        if ($resultQuery === FALSE) {
            throw new InsertionException($this->driver->error);
        }
    }

    protected function getSQLValuesToInsert($expense, array $insertHeader): array
    {
        $properties = [];
        $expenseProperties = $expense->asArray();
        foreach ($insertHeader as $key => $type) {
            $value = $this->tryGettingValueFromKey($key, $expenseProperties, $type);
            $properties[$key] = $value;
        }
        return $properties;
    }

    protected function tryGettingValueFromKey($key, $expenseProperties, $type)
    {
        if (isset($expenseProperties[strtolower($key)]) !== TRUE) {
            if (strpos(strtolower($key), 'id') === FALSE) {
                throw new InsertionKeyException($key);
            }
            $equivalentID = 1;
            return $equivalentID;
        }
        $value = $expenseProperties[strtolower($key)];
        if(strcmp(gettype($value),$type)){
            throw new WrongTypeKeyException($key, $value, $type);
        }
        return "'" . $this->driver->real_escape_string($value) . "'";
    }

    public function getExpensesForAccountID($accountID){
        $expenses = [];
        $query = 'SELECT * FROM '.$this->name.' WHERE ACCOUNT_ID = '.
            $this->driver->real_escape_string($accountID);
        $results = $this->driver->query($query);
        while($row = $results->fetch_assoc ()){
            $row["CATEGORY"] = $this->categoriesTable->getCategoryFromID($row["CATEGORY_ID"]);
            $row["SUB_CATEGORY"] = $this->subCategoriesTable->getSubCategoryFromID($row["SUB_CATEGORY_ID"]);
            $row["PAYEE"] = $this->payeesTable->getPayeeFromID($row["PAYEE_ID"]);
            $row["CURRENCY"] = $this->currenciesTable->getCurrencyFromID($row["CURRENCY_ID"]);
            $row["STATE"] = $this->statesTable->getStateFromID($row["STATE_ID"]);
            $expenses[] = new Expense($row);
        }
        return $expenses;
    }

    public function getCategoriesTable(){
        return $this->categoriesTable;
    }

    public function getSubCategoriesTable(){
        return $this->subCategoriesTable;
    }

    public function getPayeesTable(){
        return $this->payeesTable;
    }

    public function getCurrenciesTable(){
        return $this->currenciesTable;
    }

    public function getStatesTable(){
        return $this->statesTable;
    }
}