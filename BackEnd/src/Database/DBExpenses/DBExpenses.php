<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 5:17 PM
 */

namespace BackEnd\Database\DBExpenses;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBCurrencies\DBCurrencies;
use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Database\DBPayees\DBPayees;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Database\DBTables;
use BackEnd\Database\DBUsers\DBUsers;

use BackEnd\Database\DBTable;
use BackEnd\Expense;
use BackEnd\Database\InsertionException;

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
    /** @var DBCategories */
    private $categoriesTable;
    /** @var DBSubCategories */
    private $subCategoriesTable;
    /** @var DBPayees */
    private $payeesTable;
    /** @var DBCurrencies */
    private $currenciesTable;
    /** @var DBExpenseStates */
    private $statesTable;
    public function __construct($database){
        parent::__construct($database, "expenses");
        $this->categoriesTable = $database->getTableByName(DBTables::CATEGORIES);
        $this->subCategoriesTable = $database->getTableByName(DBTables::SUBCATEGORIES);
        $this->payeesTable = $database->getTableByName(DBTables::PAYEES);
        $this->currenciesTable = $database->getTableByName(DBTables::CURRENCIES);
        $this->statesTable = $database->getTableByName(DBTables::EXPENSES_STATES);
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

    /**
     * @param $expense
     * @throws InsertionException
     * @return int insertion id
     */
    public function addExpense($expense){
        $query = $this->getInsertExpenseQuery($expense);
        $this->tryAddingExpense($query);
        return $this->driver->insert_id;
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

    /**
     * @param string $query
     * @throws InsertionException
     */
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

    public function doesExpenseIDExist($expenseID)
    {
        $query = "SELECT ID FROM " . $this->name . " WHERE ID = " . $this->driver->real_escape_string($expenseID);
        $result = $this->driver->query($query);
        if ($result->num_rows == 0) {
            return false;
        }
        return true;
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
            $row["STATE"] = $this->statesTable->getExpenseStateFromID($row["STATE_ID"]);
            $expenses[] = new Expense($row);
        }
        return $expenses;
    }

    public function deleteExpense($expenseID)
    {
        $query = "DELETE FROM " . $this->name . " WHERE ID='" . $this->driver->real_escape_string($expenseID) . "'";
        $this->driver->query($query);
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