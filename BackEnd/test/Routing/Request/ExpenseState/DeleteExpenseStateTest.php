<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 9:51 PM
 */

use BackEnd\Routing\Request\ExpenseState\DeleteExpenseState;
use PHPUnit\Framework\TestCase;
use \BackEnd\Database\DBExpenseStates\DBExpenseStates;

class DeleteExpenseStateTest extends TestCase
{
    /** @var DBExpenseStates */
    private $expenseStatesTable;
    private $expenseState;
    public function setUp(){
        $this->expenseState = array("state_id" => 2);
        $this->expenseStatesTable = $this->getMockBuilder(DBExpenseStates::class)->disableOriginalConstructor()
            ->setMethods(['deleteState'])->getMock();
    }

    public function test__construct(){
        $mandatoryFields = ["state_id"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->expenseStatesTable, $request->getExpenseStatesTable());
    }

    public function testGetResponse(){
        $request = $this->createRequest();
        $this->expenseStatesTable->expects($this->once())
            ->method('deleteState')
            ->with($this->expenseState["state_id"]);

        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    public function testInitializationWithMissingParameters()
    {
        $this->expenseState = array();
        $request = $this->createRequest();
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }

    /**
     * @return DeleteExpenseState
     */
    protected function createRequest(){
        return new DeleteExpenseState($this->expenseStatesTable, $this->expenseState);
    }
}
