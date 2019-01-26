<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 5:17 PM
 */

namespace BackEnd\Database;
use mysql_xdevapi\Exception;

require_once ("DBTable.php");

class AddException extends \Exception{
    public function __construct($message){
        parent::__construct("Could not add expense in table expenses: ".$message);
    }
}

class InsertionKeyException extends \Exception{
    public function __construct($key){
        parent::__construct($key." should not be empty.");
    }
}
class WrongTypeKeyException extends \Exception{
    public function __construct($key, $value, $type){
        parent::__construct($key . " with value ".$value." has an invalid type: ".gettype($value)." instead of ".$type.".");
    }
}

class DBExpenses extends DBTable
{
    private $header = ["ID" => "integer",
        "LOCATION" => "string",
        "ACCOUNT_ID" => "integer",
        "PAYEE_ID" => "integer",
        "CATEGORY_ID" => "integer",
        "SUB_CATEGORY_ID" => "integer",
        "ADDED_DATE" => "string",
        "EXPENSE_DATE" => "string",
        "AMOUNT" => "double",
        "CURRENCY_ID" => "integer",
        "STATE_ID" => "integer"];
    public function __construct($database){
        parent::__construct($database, "expenses");
    }

    public function getTableHeader(){
        return "ID int(11) AUTO_INCREMENT UNIQUE,
            LOCATION char(50) NOT NULL,
            ACCOUNT_ID int(11) NOT NULL,
            PAYEE_ID int(11) NOT NULL,
            CATEGORY_ID int(11) NOT NULL,
            SUB_CATEGORY_ID int(11) NOT NULL,
            ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
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
            throw new AddException($this->driver->error);
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
        if ($key !== "ADDED_DATE") {
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
            return "'" . $value . "'";
        }
        return "NOW()";
    }
}