<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 1:51 PM
 */

namespace BackEnd\Tests\Routing\Request\Account;

use BackEnd\Routing\Request\Account\DeleteAccount;
use PHPUnit\Framework\TestCase;

class DeleteAccountTest extends TestCase
{
    private $usersTable;
    private $accountsTable;
    private $user;
    private $account;

    public function setUp(){
        $this->account = array("name" => "Current",
            "user_id" => 453,
            "session_id" => "1234567891234567");
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->accountsTable = $this->getMockBuilder(\BackEnd\Database\DBAccounts\DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['doesAccountExists', 'deleteAccountFromNameAndUser'])->getMock();
        $this->user = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connectWithSessionID'])->getMock();
    }


    public function test__construct(){
        $mandatoryFields = ["name", "session_id", "user_id"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->accountsTable, $request->getAccountsTable());
        $this->assertEquals($this->usersTable, $request->getUsersTable());
    }

    public function testGetResponse(){
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $this->user->expects($this->once())
            ->method('connectWithSessionID')
            ->with($this->usersTable, $this->account["session_id"], $this->account["user_id"]);
        $this->accountsTable->expects($this->once())
            ->method('doesAccountExists')
            ->with($this->account["name"], $this->account["user_id"])->will($this->returnValue(true));
        $this->accountsTable->expects($this->once())
            ->method('deleteAccountFromNameAndUser')->with($this->account["name"], $this->account["user_id"]);
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

    public function testGetResponseWithInvalidAccount(){
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()
            ->will($this->returnValue(true));
        $this->accountsTable->expects($this->once())
            ->method('doesAccountExists')
            ->with()
            ->will($this->returnValue(false));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertContains("account with Name", $response["ERROR_MESSAGE"]);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }

    public function testInitializationWithMissingParameters()
    {
        $this->account = array();
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
        $deleteAccountRequest = new DeleteAccount($this->accountsTable, $this->usersTable, $this->user, $this->account);
        return $deleteAccountRequest;
    }
}
