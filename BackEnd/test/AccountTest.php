<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/25/2019
 * Time: 9:25 PM
 */
namespace BackEnd\Tests;
use PHPUnit\Framework\TestCase;
use BackEnd\Account;
use BackEnd\Expense;

class AccountTest extends TestCase
{
    private $accountName = "Savings";
    private $tableID = 1;
    private $currentAmount = 4452;
    private $account = ["NAME" => "Savings", "ID" => "1", "CURRENT_AMOUNT" => 4452];

    public function test__construct()
    {
        $account = new Account($this->accountName, $this->tableID, $this->currentAmount);
        $this->assertEquals($this->accountName, $account->getName());
        $this->assertEquals($this->currentAmount, $account->getCurrentAmount());
        $this->assertEquals($this->tableID, $account->getTableID());
    }

    public function test__constructWithDefaultAccountValue()
    {
        $account = new Account($this->accountName, $this->tableID);
        $this->assertEquals(0, $account->getCurrentAmount());
    }

    public function testAsDict()
    {
        $account = new Account($this->accountName, $this->tableID, $this->currentAmount);
        $dictAccount = $account->asDict();
        $this->assertArraySubset($this->account, $dictAccount);
    }

    public function testLoadExpenses()
    {
        $expense = new Expense([]);
        $expensesTable = $this->getMockBuilder(\BackEnd\Database\DBExpenses::class)->disableOriginalConstructor()
            ->setMethods(['getExpensesForAccountID'])->getMock();
        $expensesTable->expects($this->once())
            ->method('getExpensesForAccountID')->with($this->tableID)->will($this->returnValue([$expense]));
        $account = new Account($this->accountName, $this->tableID, $this->currentAmount);
        $account->loadExpenses($expensesTable);
        $this->assertArraySubset([$expense], $account->getExpenses());
    }
}
