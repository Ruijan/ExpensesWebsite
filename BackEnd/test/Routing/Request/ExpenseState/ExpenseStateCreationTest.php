<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 8:58 PM
 */

use BackEnd\Routing\Request\ExpenseState\ExpenseStateCreation;
use PHPUnit\Framework\TestCase;

class ExpenseStateCreationTest extends TestCase
{
    private $usersTable;
    private $expenseStatesTable;
    private $user;
    private $expenseState;

    public function setUp()
    {
        $this->expenseState = array("name" => "Paid",
            "user_id" => 453,
            "session_id" => "1234567891234567");
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->expenseStatesTable = $this->getMockBuilder(\BackEnd\Database\DBExpenseStates\DBExpenseStates::class)->disableOriginalConstructor()
            ->setMethods(['addState'])->getMock();
        $this->user = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connectWithSessionID'])->getMock();
    }
    public function test__construct()
    {
        $mandatoryFields = ["name", "session_id", "user_id"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->expenseStatesTable, $request->getExpenseStatesTable());
        $this->assertEquals($this->usersTable, $request->getUsersTable());
    }

    public function testExecute()
    {
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $this->user->expects($this->once())
            ->method('connectWithSessionID')
            ->with($this->usersTable, $this->expenseState["session_id"], $this->expenseState["user_id"]);
        $this->expenseStatesTable->expects($this->once())
            ->method('addState');
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    public function testGetResponseWithInvalidSession()
    {
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()
            ->will($this->returnValue(false));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Invalid user session", $response["ERROR_MESSAGE"]);
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
     * @return ExpenseStateCreation
     */
    protected function createRequest()
    {
        return new ExpenseStateCreation($this->expenseStatesTable, $this->usersTable, $this->user, $this->expenseState);
    }
}
