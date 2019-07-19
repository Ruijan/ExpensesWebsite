<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 10:08 PM
 */

use BackEnd\Routing\Request\ExpenseState\RetrieveAllExpenseStates;
use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class RetrieveAllExpenseStatesTest extends ConnectedRequestTest
{
    protected $expenseStatesTable;
    protected $expenseState;

    public function setUp(){
        parent::setUp();
        $this->expenseState = array('name' => 'Locked');
        $this->data = array("user_id" => 453,
            "session_id" => "1234567891234567");
        $this->expenseStatesTable = $this->getMockBuilder(DBExpenseStates::class)->disableOriginalConstructor()
            ->setMethods(['getAllExpenseStates'])->getMock();
    }

    public function test__construct(){
        parent::test__construct();
        $this->assertEquals($this->expenseStatesTable, $this->request->getExpenseStatesTable());
    }

    public function testExecute()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();

        $this->expenseStatesTable->expects($this->once())
            ->method('getAllExpenseStates')
            ->with()->will($this->returnValue(array($this->expenseState)));
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


    protected function createRequest(){
        $this->request = new RetrieveAllExpenseStates($this->expenseStatesTable, $this->usersTable, $this->user, $this->data);
    }
}
