<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/25/2019
 * Time: 9:25 PM
 */
namespace BackEnd\Tests;
use Backend\Account\MissingParametersException;
use PHPUnit\Framework\TestCase;
use BackEnd\Account\Account;
use BackEnd\Expense;

class AccountTest extends TestCase
{
    private $tableID = 1;
    private $accountArray = ["name" => "Savings",
        "id" => 1,
        "current_amount" => 4452,
        "currency" => "CHF",
        "currency_id" => 1,
        "added_date" => "2018/01/01 20:00:05",
        "user_id" => 1,
        "user" => "Julien"];

    public function test__construct()
    {
        $account = new Account($this->accountArray);
        $this->assertEquals($this->accountArray, $account->asDict());
    }

    public function test__constructWithoutNameShouldThrow()
    {
        unset($this->accountArray["name"]);
        $this->expectException(MissingParametersException::class);
        new Account($this->accountArray);
    }

    public function test__constructWithoutCurrencyShouldThrow()
    {
        unset($this->accountArray["currency"]);
        $this->expectException(MissingParametersException::class);
        new Account($this->accountArray);
    }

    public function test__constructWithWrongKeyShouldThrow()
    {
        unset($this->accountArray["currency_id"]);
        $this->accountArray["currency_i"] = 1;
        $this->expectException(\Exception::class);
        new Account($this->accountArray);
    }


    public function testLoadExpenses()
    {
        $expense = new Expense([]);
        $expensesTable = $this->getMockBuilder(\BackEnd\Database\DBExpenses\DBExpenses::class)->disableOriginalConstructor()
            ->setMethods(['getExpensesForAccountID'])->getMock();
        $expensesTable->expects($this->once())
            ->method('getExpensesForAccountID')->with($this->tableID)->will($this->returnValue([$expense]));
        $account = new Account($this->accountArray);
        $account->loadExpenses($expensesTable);
        $this->assertArraySubset([$expense], $account->getExpenses());
    }
}
