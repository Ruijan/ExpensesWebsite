<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/24/2019
 * Time: 7:36 PM
 */

use BackEnd\Routing\Request\Expense\DeleteExpense;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use \BackEnd\Database\DBExpenses\DBExpenses;

class DeleteExpenseTest extends ConnectedRequestTest
{
    protected $expensesTable;
    public function setUp(){
        $this->data = array("expense_id" => 2);
        parent::setUp();
        $this->expensesTable = $this->getMockBuilder(DBExpenses::class)->disableOriginalConstructor()
            ->setMethods(['deleteExpense', 'doesExpenseIDExist'])->getMock();
    }

    public function test__construct(){
        $this->mandatoryFields[] = "expense_id";
        parent::test__construct();
        $this->assertEquals($this->expensesTable, $this->request->getExpensesTable());
    }

    public function testExecute(){
        $this->createRequest();
        $this->expensesTable->expects($this->once())
            ->method('deleteExpense')
            ->with($this->data["expense_id"]);
        $this->expensesTable->expects($this->once())
            ->method('doesExpenseIDExist')
            ->with($this->data["expense_id"])
            ->will($this->returnValue(true));
        $this->connectSuccessfullyUser();

        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    public function testExecuteFails(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->expensesTable->expects($this->once())
            ->method('doesExpenseIDExist')
            ->with($this->data["expense_id"])
            ->will($this->returnValue(false));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }

    protected function createRequest()
    {
        $this->request = new DeleteExpense($this->expensesTable, $this->usersTable, $this->user, $this->data);

    }
}
