<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/25/2019
 * Time: 9:11 PM
 */

use BackEnd\Routing\Request\Expense\RetrieveAllExpenses;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use \BackEnd\Database\DBExpenses\DBExpenses;
use \BackEnd\Expense;

class RetrieveAllExpensesTest extends ConnectedRequestTest
{
    protected $expensesTable;
    protected $expense;

    public function setUp()
    {
        parent::setUp();
        $this->expense = $this->getMockBuilder(Expense::class)->disableOriginalConstructor()
            ->setMethods(['asDict'])->getMock();
        $this->expensesTable = $this->getMockBuilder(DBExpenses::class)->disableOriginalConstructor()
            ->setMethods(['getAllExpensesForUser'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->expensesTable, $this->request->getExpensesTable());
    }

    public function testExecute()
    {
        $expenses = array(
            "expense_id" => 2
        );
        $this->createRequest();
        $this->connectSuccessfullyUser();

        $this->expensesTable->expects($this->once())
            ->method('getAllExpensesForUser')
            ->with($this->data["user_id"])->will($this->returnValue(array($this->expense)));
        $this->expense->expects($this->once())
            ->method('asDict')
            ->with()->will($this->returnValue($expenses));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        if ($response["STATUS"] == "ERROR") {
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        } else {
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    protected function createRequest()
    {
        $this->request = new RetrieveAllExpenses($this->expensesTable, $this->usersTable, $this->user, $this->data);
    }
}
