<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/13/2019
 * Time: 9:50 PM
 */

use PHPUnit\Framework\TestCase;
use BackEnd\Routing\Request\Account\AccountCreation;

class AccountCreationTest extends TestCase
{
    private $usersTable;
    private $accountsTable;
    public function setUp()
    {
        $_POST = array("name" => "Current",
            "currency" => "EUR",
            "current_amount" => "4061.68",
            "user_key" => "123456daw7894521d3wa687");
        $this->accountsTable = $this->getMockBuilder(\BackEnd\Database\DBAccounts\DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['addAccount'])->getMock();
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
    }

    public function testInitialization(){
        $accountCreationRequest = new AccountCreation($this->accountsTable, $this->usersTable);
        $accountCreationRequest->init();
        $this->assertEquals($_POST["name"], $accountCreationRequest->getName());
        $this->assertEquals($_POST["currency"], $accountCreationRequest->getCurrency());
        $this->assertEquals($_POST["current_amount"], $accountCreationRequest->getCurrentAmount());
        $this->assertEquals($_POST["user_key"], $accountCreationRequest->getUserKey());
        $this->assertEquals($this->accountsTable, $accountCreationRequest->getAccountsTable());
        $this->assertEquals($this->usersTable, $accountCreationRequest->getUsersTable());
    }

    public function testGetResponse(){
        $accountCreationRequest = new AccountCreation($this->accountsTable, $this->usersTable);
        $accountCreationRequest->init();
        $this->usersTable->expects($this->once())
            ->method('isUserSessionKeyValid')
            ->with($_POST["user_key"])
            ->will($this->returnValue(true));
        $response = $accountCreationRequest->getResponse();
        $this->assertEquals(\BackEnd\Routing\Response\Account\AccountCreation::class, get_class($response));
    }
}
