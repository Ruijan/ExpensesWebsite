<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/12/2019
 * Time: 10:48 PM
 */

namespace BackEnd\Tests\Routing\Request\Account;

use BackEnd\Account\Account;
use BackEnd\Database\DBAccounts\DBAccounts;
use BackEnd\Routing\Request\Account\RetrieveAccounts;
use \BackEnd\Database\DBUsers\DBUsers;
use BackEnd\User;
use PHPUnit\Framework\TestCase;

class RetrieveAccountsTest extends TestCase
{
    private $usersTable;
    private $accountsTable;
    private $user;
    private $account;
    private $data;

    public function setUp(){
        $this->account = $this->getMockBuilder(Account::class)->disableOriginalConstructor()
            ->setMethods(['asDict'])->getMock();
        $this->data = array("user_id" => 453,
            "session_id" => "1234567891234567");
        $this->usersTable = $this->getMockBuilder(DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->accountsTable = $this->getMockBuilder(DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['getAccountsFromUserId'])->getMock();
        $this->user = $this->getMockBuilder(User::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connectWithSessionID'])->getMock();
    }

    public function testExecute()
    {
        $account = array(
            "name" => "Current",
            "currency" => "EUR",
            "current_amount" => 13456.3
            );
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $this->user->expects($this->once())
            ->method('connectWithSessionID')
            ->with($this->usersTable, $this->data["session_id"], $this->data["user_id"]);
        $this->account->expects($this->once())
            ->method('asDict')
            ->with()->will($this->returnValue($account));
        $this->accountsTable->expects($this->once())
            ->method('getAccountsFromUserId')
            ->with($this->data["user_id"])->will($this->returnValue(array($this->account)));
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

    public function test__construct(){
        $mandatoryFields = ["session_id", "user_id"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->accountsTable, $request->getAccountsTable());
        $this->assertEquals($this->usersTable, $request->getUsersTable());
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
        $deleteAccountRequest = new RetrieveAccounts($this->accountsTable, $this->usersTable, $this->user, $this->data);
        return $deleteAccountRequest;
    }
}
