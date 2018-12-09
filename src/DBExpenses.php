<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 5:17 PM
 */

namespace src;
use mysql_xdevapi\Exception;

require_once ("DBTable.php");

class DBExpenses extends DBTable
{
    private $header = ["ID", "LOCATION", "PAYER_ID", "PAYEE_ID", "CATEGORY_ID", "SUB_CATEGORY_ID",
        "ADDED_DATE", "EXPENSE_DATE", "AMOUNT", "CURRENCY_ID","STATE_ID"];
    public function __construct($database){
        parent::__construct($database, "expenses");
    }

    public function getTableHeader(){
        return "ID int(11) AUTO_INCREMENT UNIQUE,
            LOCATION char(50) NOT NULL,
            PAYER_ID int(11) NOT NULL,
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

    /**
     * @param $expense
     * @return string
     */
    protected function getInsertExpenseQuery($expense): string
    {
        $insertHeader = array_slice($this->header, 1);
        $properties = $this->getSQLValuesToInsert($expense, $insertHeader);
        $properties = implode(", ", $properties);
        $header = implode(", ", array_slice($this->header, 1));
        $query = 'INSERT INTO ' . $this->name . ' (' . $header . ') VALUES (' . $properties . ')';
        return $query;
    }

    /**
     * @param string $query
     * @throws \Exception
     */
    protected function tryAddingExpense(string $query): void
    {
        $resultQuery = $this->driver->query($query);
        if ($resultQuery === FALSE) {
            print($query);
            print($this->driver->error);
            throw new \Exception($this->driver->error);
        }
    }

    /**
     * @param $expense
     * @param array $insertHeader
     * @return array
     */
    protected function getSQLValuesToInsert($expense, array $insertHeader): array
    {
        $properties = [];
        $expenseProperties = $expense->asArray();
        foreach ($insertHeader as $key) {
            if ($key !== "ADDED_DATE") {
                if(isset($expenseProperties[strtolower($key)]) !== TRUE){
                    if(strpos(strtolower($key), 'id')){
                        $id = 1;
                        $properties[$key] = $id;
                    }
                    else{
                        throw new \Exception($key." should not be empty.");
                    }
                }
                else {
                    $properties[$key] = "'" . $expenseProperties[strtolower($key)] . "'";
                }
            } else {
                $properties["ADDED_DATE"] = "NOW()";
            }
        }
        return $properties;
    }
}