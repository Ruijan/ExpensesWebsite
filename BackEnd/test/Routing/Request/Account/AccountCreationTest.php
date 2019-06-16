<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/13/2019
 * Time: 9:50 PM
 */

namespace BackEnd\Tests\Routing\Request\Account;

use PHPUnit\Framework\TestCase;
use BackEnd\Routing\Request\Account\AccountCreation;

class AccountCreationTest extends TestCase
{
    private $usersTable;
    private $accountsTable;
    private $user;
    private $account;

    public function setUp()
    {
        $this->account = array("name" => "Current",
            "currency_id" => 5,
            "current_amount" => "4061.68",
            "user_id" => 453,
            "session_id" => "1234567891234567");
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->accountsTable = $this->getMockBuilder(\BackEnd\Database\DBAccounts\DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['addAccount'])->getMock();
        $this->user = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connectWithSessionID'])->getMock();
    }

    public function test__construct()
    {
        $mandatoryFields = ["name", "session_id", "user_id", "current_amount", "currency_id"];
        $request = $this->createRequest();
        $this->assertTrue( sizeof(array_diff($mandatoryFields, $request->getMandatoryFields())) == 0);
        $this->assertEquals($this->accountsTable, $request->getAccountsTable());
        $this->assertEquals($this->usersTable, $request->getUsersTable());
    }

    protected function createRequest()
    {
        $accountCreationRequest = new AccountCreation($this->accountsTable, $this->usersTable, $this->user, $this->account);
        return $accountCreationRequest;
    }


    public function testGetResponse()
    {
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $this->user->expects($this->once())
            ->method('connectWithSessionID')
            ->with($this->usersTable, $this->account["session_id"], $this->account["user_id"]);
        $this->accountsTable->expects($this->once())
            ->method('addAccount');
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
}
