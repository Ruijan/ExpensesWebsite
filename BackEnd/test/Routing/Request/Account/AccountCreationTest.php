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
    public function setUp()
    {
        $_POST = array("name" => "Current",
            "currency_id" => 5,
            "current_amount" => "4061.68",
            "user_key" => "123456daw7894521d3wa687",
            "user_id" => 453);
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->user = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isConnected'])->getMock();
    }

    public function testInitialization(){
        $accountCreationRequest = $this->createRequest();
        $this->assertEquals($_POST["name"], $accountCreationRequest->getName());
        $this->assertEquals($_POST["currency_id"], $accountCreationRequest->getCurrencyID());
        $this->assertEquals($_POST["current_amount"], $accountCreationRequest->getCurrentAmount());
        $this->assertEquals($_POST["user_id"], $accountCreationRequest->getUserID());
        $this->assertEquals($this->accountsTable, $accountCreationRequest->getAccountsTable());
        $this->assertEquals($this->usersTable, $accountCreationRequest->getUsersTable());
    }

    public function testGetResponse(){
        $accountCreationRequest = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $response = $accountCreationRequest->getResponse();
        $this->assertEquals(\BackEnd\Routing\Response\Account\AccountCreation::class, get_class($response));
    }

    public function testGetResponseWithInvalidSession(){
        $accountCreationRequest = new AccountCreation($this->accountsTable, $this->usersTable, $this->user);
        $accountCreationRequest->init();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()
            ->will($this->returnValue(false));
        $this->expectException(\Backend\Routing\Request\Connection\InvalidSessionException::class);
        $response = $accountCreationRequest->getResponse();
        $this->assertEquals(\BackEnd\Routing\Response\Account\AccountCreation::class, get_class($response));
    }

    public function testInitializationWithMissingAmountShouldThrow(){
        $_POST = array("name" => "Current",
            "currency_id" => 5,
            "user_id" => 453);
        $this->expectException(\BackEnd\Routing\Request\MissingParametersException::class);
        $this->createRequest();
    }

    public function testInitializationWithMissingCurrencyShouldThrow(){
        $_POST = array("name" => "Current",
            "current_amount" => "4061.68",
            "user_id" => 453);
        $this->expectException(\BackEnd\Routing\Request\MissingParametersException::class);
        $this->createRequest();
    }

    public function testInitializationWithMissingNameShouldThrow(){
        $_POST = array("currency_id" => 5,
            "current_amount" => "4061.68",
            "user_id" => 453);
        $this->expectException(\BackEnd\Routing\Request\MissingParametersException::class);
        $this->createRequest();
    }

    public function testInitializationWithMissingUserIDShouldThrow(){
        $_POST = array("name" => "Current",
            "currency_id" => 5,
            "current_amount" => "4061.68");
        $this->expectException(\BackEnd\Routing\Request\MissingParametersException::class);
        $this->createRequest();
    }


    protected function createRequest(){
        $accountCreationRequest = new AccountCreation($this->accountsTable, $this->usersTable, $this->user);
        $accountCreationRequest->init();
        return $accountCreationRequest;
    }
}
