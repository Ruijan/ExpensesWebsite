<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 4:59 PM
 */

require_once("TableCreationTest.php");
require_once(str_replace("test", "src", __DIR__."/").'DBExpenses.php');

class DBExpensesTest extends TableCreationTest
{
    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "LOCATION" => "char(50)",
            "PAYER_ID" => "int(11)",
            "PAYEE_ID" => "int(11)",
            "CATEGORY_ID" => "int(11)",
            "SUB_CATEGORY_ID" => "int(11)",
            "ADDED_DATE" => "datetime",
            "EXPENSE_DATE" => "datetime",
            "AMOUNT" => "double",
            "CURRENCY_ID" => "int(11)",
            "STATE" => "int(11)"];
        $this->name = "expenses";
    }

    public function createTable()
    {
        $this->table = new \src\DBExpenses($this->database);
    }
}
