<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 10:08 PM
 */

use BackEnd\Routing\Request\ExpenseState\RetrieveAllExpenseStates;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\User;

class RetrieveAllExpenseStatesTest extends TestCase
{
    protected $expenseStatesTable;
    protected $usersTable;
    protected $user;
    protected $data;
    protected $expenseState;

    public function setUp(){
        $this->expenseState = array('name' => 'Locked');
        $this->data = array("user_id" => 453,
            "session_id" => "1234567891234567");
        $this->usersTable = $this->getMockBuilder(DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->expenseStatesTable = $this->getMockBuilder(DBExpenseStates::class)->disableOriginalConstructor()
            ->setMethods(['getAllExpenseStates'])->getMock();
        $this->user = $this->getMockBuilder(User::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connectWithSessionID'])->getMock();
    }

    public function test__construct(){
        $mandatoryFields = ["session_id", "user_id"];
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
            ->with($this->usersTable, $this->data["session_id"], $this->data["user_id"]);

        $this->expenseStatesTable->expects($this->once())
            ->method('getAllExpenseStates')
            ->with()->will($this->returnValue(array($this->expenseState)));
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

    public function testGetResponseWithInvalidSession(){
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()
            ->will($this->returnValue(false));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertContains("Invalid user", $response["ERROR_MESSAGE"]);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }

    public function testInitializationWithMissingParameters()
    {
        $this->data = array();
        $request = $this->createRequest();
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }

    protected function createRequest(){
        $request = new RetrieveAllExpenseStates($this->expenseStatesTable, $this->usersTable, $this->user, $this->data);
        return $request;
    }
}
