<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/17/2019
 * Time: 9:58 PM
 */
namespace BackEnd\Tests\Routing\Response\Account;
use BackEnd\Routing\Response\Account\AccountCreation;
use PHPUnit\Framework\TestCase;

class AccountCreationTest extends TestCase
{
    private $request;
    private $usersTable;
    private $accountsTable;
    private $account;
    public function setUp()
    {
        $this->request = $this->getMockBuilder(\BackEnd\Routing\Request\Account\AccountCreation::class)->disableOriginalConstructor()
            ->setMethods(['getAccountsTable', 'getUsersTable', 'getUserID'])->getMock();
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['updateSession'])->getMock();
        $this->accountsTable = $this->getMockBuilder(\BackEnd\Database\DBAccounts\DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['addAccount'])->getMock();
        $this->account = $this->getMockBuilder(\BackEnd\Account\Account::class)->disableOriginalConstructor()
            ->getMock();
    }

    public function test__construct()
    {
        $response = $this->createResponse();
        $this->assertEquals($this->request, $response->getRequest());
        $this->assertEquals($this->usersTable, $response->getUsersTable());
        $this->assertEquals($this->accountsTable, $response->getAccountsTable());
        $this->assertEquals($this->account, $response->getAccount());
    }

    public function testExecute()
    {
        $this->request->expects($this->once())
            ->method('getUserID')->with()->will($this->returnValue("5"));
        $this->usersTable->expects($this->once())
            ->method('updateSession')->with();
        $this->accountsTable->expects($this->once())
            ->method('addAccount')->with($this->account);
        $response = $this->createResponse();
        $response->execute();
        $this->assertEquals('Account added', $response->getAnswer());
    }

    private function createResponse(){
        $this->request->expects($this->once())
            ->method('getAccountsTable')->with()->will($this->returnValue($this->accountsTable));
        $this->request->expects($this->once())
            ->method('getUsersTable')->with()->will($this->returnValue($this->usersTable));
        return new AccountCreation($this->request, $this->account);
    }
}
