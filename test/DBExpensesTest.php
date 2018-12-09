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
    private $expenseArray = array(
        "id" => 1,
        "payer" => "Julien",
        "payer_id" => 1,
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
            "PAYER_ID" => "int(11)",
            "PAYEE_ID" => "int(11)",
            "CATEGORY_ID" => "int(11)",
            "SUB_CATEGORY_ID" => "int(11)",
            "ADDED_DATE" => "datetime",
            "EXPENSE_DATE" => "datetime",
            "AMOUNT" => "double",
            "CURRENCY_ID" => "int(11)",
            "STATE_ID" => "int(11)"];
        $this->name = "expenses";
    }

    public function createTable()
    {
        $this->table = new \src\DBExpenses($this->database);
    }

    public function testAddExpenseWithEmptyExpenseShouldThrow(){
        $expense = parent::getMockBuilder(\Expense::class)->setMethods(['asArray'])->getMock();
        $expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($this->expenseArray));
        $this->expectException(Exception::class);
        $this->table->addExpense($expense);
        $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM '.$this->name)->fetch_all()[0][0];
        $this->assertEquals($nbExpenses, 0);

    }

    public function testAddExpense(){
        $expense = parent::getMockBuilder(\Expense::class)->setMethods(['asArray'])->getMock();
        $expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($this->expenseArray));
        $this->table->addExpense($expense);
        $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM '.$this->name)->fetch_all()[0][0];
        print_r($nbExpenses);
        $this->assertEquals($nbExpenses, 1);
    }

}
