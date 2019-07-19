<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 1:51 PM
 */

namespace BackEnd\Tests\Routing\Request\Account;

use BackEnd\Routing\Request\Account\DeleteAccount;
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use PHPUnit\Framework\TestCase;

class DeleteAccountTest extends ConnectedRequestTest
{
    private $accountsTable;

    public function setUp(){

        $this->mandatoryFields[] = "name";
        $this->data = array("name" => "Current");
            parent::setUp();
        $this->accountsTable = $this->getMockBuilder(\BackEnd\Database\DBAccounts\DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['doesAccountExists', 'deleteAccountFromNameAndUser'])->getMock();
    }


    public function test__construct(){
        parent::test__construct();
        $this->assertEquals($this->accountsTable, $this->request->getAccountsTable());
    }

    public function testGetResponse(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->accountsTable->expects($this->once())
            ->method('doesAccountExists')
            ->with($this->data["name"], $this->data["user_id"])->will($this->returnValue(true));
        $this->accountsTable->expects($this->once())
            ->method('deleteAccountFromNameAndUser')->with($this->data["name"], $this->data["user_id"]);
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
        $deleteAccountRequest = new DeleteAccount($this->accountsTable, $this->usersTable, $this->user, $this->data);
        return $deleteAccountRequest;
    }
}
