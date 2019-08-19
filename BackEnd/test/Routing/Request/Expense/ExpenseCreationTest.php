<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 6:20 PM
 */

use PHPUnit\Framework\TestCase;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use BackEnd\Routing\Request\Expense\ExpenseCreation;

class ExpenseCreationTest extends ConnectedRequestTest
{
    private $expensesTable;
    private $expense;

    public function setUp()
    {
        $this->data = array(
            "expense_date" => "ada",
            "location" => "dwawd",
            "account_id" => 5,
            "payee" => "fwad",
            "payee_id" => 1,
            "category" => "dffaw",
            "category_id" => 1,
            "sub_category" => "fga",
            "sub_category_id" => 2,
            "amount" => 123.45,
            "currency" => "EUR",
            "currency_id" => 1,
            "state" => "Paid",
            "state_id" => 1
        );
        parent::setUp();
        $this->mandatoryFields = array_keys($this->data);
        $this->expensesTable = $this->getMockBuilder(\BackEnd\Database\DBExpenses\DBExpenses::class)->disableOriginalConstructor()
            ->setMethods(['addExpense'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->expensesTable, $this->request->getExpensesTable());
    }

    public function testExecute()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->expensesTable->expects($this->once())
            ->method('addExpense');
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    public function testExecuteFails(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $exception = new \BackEnd\Database\InsertionException("", ["name"], "plop", "plop");
        $this->expensesTable->expects($this->once())
            ->method('addExpense')
            ->will($this->throwException($exception));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }

    protected function createRequest()
    {
        $this->request = new ExpenseCreation($this->expensesTable, $this->usersTable, $this->user, $this->data);
    }
}
