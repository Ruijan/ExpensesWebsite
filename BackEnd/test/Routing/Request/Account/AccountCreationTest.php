<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/13/2019
 * Time: 9:50 PM
 */

namespace BackEnd\Tests\Routing\Request\Account;

use BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use BackEnd\Routing\Request\Account\AccountCreation;
use \BackEnd\Database\DBAccounts\DBAccounts;

class AccountCreationTest extends ConnectedRequestTest
{
    private $accountsTable;

    public function setUp()
    {
        $this->data = array("name" => "Current",
            "currency_id" => 5,
            "current_amount" => "4061.68");
        parent::setUp();
        $this->mandatoryFields = array_merge($this->mandatoryFields,["name", "current_amount", "currency_id"]);
        $this->accountsTable = $this->getMockBuilder(DBAccounts::class)->disableOriginalConstructor()
            ->setMethods(['addAccount'])->getMock();
    }

    public function test__construct()
    {

        parent::test__construct();
        $this->assertEquals($this->accountsTable, $this->request->getAccountsTable());
    }

    protected function createRequest()
    {
        $this->request = new AccountCreation($this->accountsTable, $this->usersTable, $this->user, $this->data);
    }


    public function testGetResponse()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->accountsTable->expects($this->once())
            ->method('addAccount');
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }
}
