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

class DeleteAccountTest extends ConnectedRequestTest
{
    private $accountsTable;

    public function setUp()
    {
        $this->data = array("name" => "Current");
        parent::setUp();
        $this->mandatoryFields[] = "name";
        $this->accountsTable = $this->getMockBuilder(\BackEnd\Database\DBAccounts\DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['doesAccountExists', 'deleteAccountFromNameAndUser'])->getMock();
    }


    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->accountsTable, $this->request->getAccountsTable());
    }

    public function testGetResponse()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->accountsTable->expects($this->once())
            ->method('doesAccountExists')
            ->with($this->data["name"], $this->data["user_id"])->will($this->returnValue(true));
        $this->accountsTable->expects($this->once())
            ->method('deleteAccountFromNameAndUser')->with($this->data["name"], $this->data["user_id"]);
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
        $this->request = new DeleteAccount($this->accountsTable, $this->usersTable, $this->user, $this->data);
    }

    public function testGetResponseWithInvalidAccount()
    {
        $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()
            ->will($this->returnValue(true));
        $this->accountsTable->expects($this->once())
            ->method('doesAccountExists')
            ->with()
            ->will($this->returnValue(false));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertContains("account with Name", $response["ERROR_MESSAGE"]);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }
}
