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
    private $expense;
    private $expenseArray = array(
        "id" => 1,
        "account_id" => 12,
        "expense_date" => "2018-01-01 00:00:00",
        "location" => "Lausanne",
        "payee" => "Migros",
        "payee_id" => 1,
        "category" => "Food",
        "category_id" => 1,
        "sub_category" => "Groceries",
        "sub_category_id" => 1,
        "amount" => 60.52,
        "currency" => "CHF",
        "currency_id" => 1,
        "state" => "Paid",
        "state_id" => 2
    );

    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "LOCATION" => "char(50)",
            "ACCOUNT_ID" => "int(11)",
            "PAYEE_ID" => "int(11)",
            "CATEGORY_ID" => "int(11)",
            "SUB_CATEGORY_ID" => "int(11)",
            "ADDED_DATE" => "datetime",
            "EXPENSE_DATE" => "datetime",
            "AMOUNT" => "double",
            "CURRENCY_ID" => "int(11)",
            "STATE_ID" => "int(11)"];
        $this->name = "expenses";
        $this->expense = parent::getMockBuilder(\Expense::class)->setMethods(['asArray'])->getMock();
    }

    public function createTable()
    {
        $this->table = new \src\DBExpenses($this->database);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddExpenseWithWrongStateShouldThrow(){
        $wrongExpenseArray = $this->expenseArray;
        $wrongExpenseArray["state_id"] = "Paid";
        $this->expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($wrongExpenseArray));

        try{
            $this->table->addExpense($this->expense);
            $this->assertTrue(False);
        }
        catch(\src\WrongTypeKeyException $e){
            $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM '.$this->name)->fetch_all();
            $this->assertEquals(0, $nbExpenses[0][0]);
        }
    }

    public function testAddExpense(){
        $this->expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($this->expenseArray));
        $this->table->addExpense($this->expense);
        $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM '.$this->name)->fetch_all()[0][0];
        $this->assertEquals(1, $nbExpenses);
    }

    public function testAddExpenseWithNoID(){
        $noIDExpense = [];
        foreach($this->expenseArray as $key => $value){
            $noIDExpense[$key] = NULL;
            if(strpos($key, 'id') === false) {
                $noIDExpense[$key] = $value;
            }
        }

        $this->expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($noIDExpense));
        $this->table->addExpense($this->expense);
        $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM '.$this->name)->fetch_all()[0][0];
        $this->assertEquals(1, $nbExpenses);
    }

    public function testAddExpenseWithNoLocation(){
        $noLocationExpense = $this->expenseArray;
        $noLocationExpense["location"] = NULL;
        $this->expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($noLocationExpense));
        try{
            $this->table->addExpense($this->expense);
            $this->assertTrue(False);
        }
        catch(\src\InsertionKeyException $e){
            $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM '.$this->name)->fetch_all()[0][0];
            $this->assertEquals(0, $nbExpenses);
        }
    }
}
