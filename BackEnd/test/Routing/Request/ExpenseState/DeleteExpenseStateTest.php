<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 9:51 PM
 */

use BackEnd\Routing\Request\ExpenseState\DeleteExpenseState;
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use \BackEnd\Database\DBExpenseStates\DBExpenseStates;

class DeleteExpenseStateTest extends ConnectedRequestTest
{
    /** @var DBExpenseStates */
    private $expenseStatesTable;
    public function setUp(){
        $this->data = array("state_id" => 2);
        parent::setUp();
        $this->expenseStatesTable = $this->getMockBuilder(DBExpenseStates::class)->disableOriginalConstructor()
            ->setMethods(['deleteState'])->getMock();
    }

    public function test__construct(){
        $this->mandatoryFields[] = "state_id";
        parent::test__construct();
        $this->assertEquals($this->expenseStatesTable, $this->request->getExpenseStatesTable());
    }

    public function testExecute(){
        $this->createRequest();
        $this->expenseStatesTable->expects($this->once())
            ->method('deleteState')
            ->with($this->data["state_id"]);
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
        $exception = new \BackEnd\Database\DBExpenseStates\UndefinedExpenseStateID(5);
        $this->expenseStatesTable->expects($this->once())
            ->method('deleteState')->with($this->data["state_id"])
            ->will($this->throwException($exception));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }

    protected function createRequest(){
        $this->request = new DeleteExpenseState($this->expenseStatesTable, $this->usersTable, $this->user, $this->data);
    }
}
