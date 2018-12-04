<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 4:59 PM
 */

use PHPUnit\Framework\TestCase;
require_once(str_replace("test", "src", __DIR__."/").'DBExpenses.php');

class DBExpensesTest extends TestCase
{
    private $expenses;
    private $driver;
    private $database;
    private $columns = ["ID" => "int(11)",
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

    public function setUp(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->expenses = new \src\DBExpenses($this->database);
    }
    public function test__construct(){
        $this->assertTrue($this->driver->query("SELECT 1 FROM expenses LIMIT 1 ") !== FALSE);
        $columns = $this->driver->query("SHOW COLUMNS FROM expenses");
        $existingColumn = [];
        foreach($this->columns as $column => $value) {
            $existingColumn[$column] = 0;
        }
        $this->assertEquals($columns->num_rows, count($this->columns));
        foreach($columns as $column){
            $this->assertEquals($column["Type"], $this->columns[$column["Field"]]);
            $existingColumn[$column["Field"]] += 1;
            $this->assertEquals($existingColumn[$column["Field"]], 1);
        }
    }

    public function tearDown(){
        if($this->driver->query("SELECT 1 FROM expenses LIMIT 1 ")) {
            $this->expenses->dropTable();
        }
    }
}
