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
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use BackEnd\User;
use PHPUnit\Framework\TestCase;

class RetrieveAccountsTest extends ConnectedRequestTest
{
    private $accountsTable;
    private $account;

    public function setUp(){
        parent::setUp();
        $this->account = $this->getMockBuilder(Account::class)->disableOriginalConstructor()
            ->setMethods(['asDict'])->getMock();
        $this->accountsTable = $this->getMockBuilder(DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['getAccountsFromUserId'])->getMock();
    }

    public function testExecute()
    {
        $account = array(
            "name" => "Current",
            "currency" => "EUR",
            "current_amount" => 13456.3
            );
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->account->expects($this->once())
            ->method('asDict')
            ->with()->will($this->returnValue($account));
        $this->accountsTable->expects($this->once())
            ->method('getAccountsFromUserId')
            ->with($this->data["user_id"])->will($this->returnValue(array($this->account)));
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

    public function test__construct(){
        parent::test__construct();
        $this->assertEquals($this->accountsTable, $this->request->getAccountsTable());
    }

    protected function createRequest(){
        $this->request = new RetrieveAccounts($this->accountsTable, $this->usersTable, $this->user, $this->data);
    }
}
