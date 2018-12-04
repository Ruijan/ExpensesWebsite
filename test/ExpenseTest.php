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
        "payer" => "Julien",
        "date" => "2018-01-01 00:00:00",
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
        "payer" => "Julien",
        "Date" => "2018-01-01 00:00:00",
        "Location" => "Lausanne",
        "Payee" => "Migros",
        "Category" => "Food",
        "Sub_category" => "Groceries",
        "Amount" => 60.52,
        "Currency" => "CHF",
        "State" => "Paid"
    );

    public function test__construct(){
        $this->expense = new \src\Expense($this->expenseArray);
        $this->assertEquals($this->expenseArray, $this->expense->asArray());
    }

    public function test__constructWithCapitalKeys(){
        $this->expense = new \src\Expense($this->expenseArrayWithCapital);
        $this->assertEquals($this->expenseArray, $this->expense->asArray());
    }
}
