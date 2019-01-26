<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 10:50 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'Expense.php');

use PHPUnit\Framework\TestCase;

class ExpenseTest extends TestCase
{
    private $expense;
    private $expenseArray = array(
        "id" => 1,
        "account" => "Savings",
        "expense_date" => "2018-01-01 00:00:00",
        "location" => "Lausanne",
        "payee" => "Migros",
        "category" => "Food",
        "sub_category" => "Groceries",
        "amount" => 60.52,
        "currency" => "CHF",
        "state" => "Paid"
    );

    private $expenseArrayWithCapital = array(
        "ID" => 1,
        "Account" => "Savings",
        "Expense_Date" => "2018-01-01 00:00:00",
        "Location" => "Lausanne",
        "Payee" => "Migros",
        "Category" => "Food",
        "Sub_category" => "Groceries",
        "Amount" => 60.52,
        "Currency" => "CHF",
        "State" => "Paid"
    );

    public function test__construct(){
        $this->expense = new \BackEnd\Expense($this->expenseArray);
        $this->assertEquals($this->expenseArray, $this->expense->asPrintableArray());
    }

    public function test__constructWithCapitalKeys(){
        $this->expense = new \BackEnd\Expense($this->expenseArrayWithCapital);
        $this->assertEquals($this->expenseArray, $this->expense->asPrintableArray());
    }

    public function testAsArray(){
        $fullArray = $this->expenseArray;
        $fullArray["account_id"] = random_int(1, 100);
        $fullArray["payee_id"] = random_int(1, 100);
        $fullArray["category_id"] = random_int(1, 100);
        $fullArray["sub_category_id"] = random_int(1, 100);
        $fullArray["currency_id"] = random_int(1, 100);
        $fullArray["state_id"] = random_int(1, 100);
        $this->expense = new \BackEnd\Expense($fullArray);
        $this->assertArraySubset($fullArray, $this->expense->asArray(), true);
    }

    public function test1000CreationAsArray(){
        $fullArray = [];
        for($x = 0; $x <= 1000; $x++)
        {
            $fullArray[$x] = $this->expenseArray;
            $fullArray[$x]["account_id"] = random_int(1, 100);
            $fullArray[$x]["payee_id"] = random_int(1, 100);
            $fullArray[$x]["category_id"] = random_int(1, 100);
            $fullArray[$x]["sub_category_id"] = random_int(1, 100);
            $fullArray[$x]["currency_id"] = random_int(1, 100);
            $fullArray[$x]["state_id"] = random_int(1, 100);
        }
        $timePre = microtime(true);
        for($x = 0; $x <= 1000; $x++)
        {
            $this->expense = new \BackEnd\Expense($fullArray[$x]);
            $this->assertArraySubset($fullArray[$x], $this->expense->asArray(), true);
        }
        $timePost = microtime(true);
        $exec_time = ($timePost - $timePre);

        echo "Time to create and obtain 1000 Expenses in array: ".round($exec_time, 4)."s";
        $this->assertTrue($exec_time < 1);
    }
}
