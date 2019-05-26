<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 9:27 PM
 */

namespace Request;

use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\Account\AccountCreation;
use BackEnd\Routing\Request\Account\AccountRequestFactory;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\Database;

class AccountRequestFactoryTest extends TestCase
{
    private $database;
    public function setUp()
    {
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()
            ->setMethods(["getDriver", "getTableByName"])->getMock();
    }

    public function test__construct(){
        $factory = new AccountRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateAccountCreationRequest(){
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::USERS], [DBTables::ACCOUNTS]);
        $factory = new AccountRequestFactory($this->database);
        $request = $factory->createRequest("Create");
        $this->assertEquals(AccountCreation::class, get_class($request));
    }


    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new AccountRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut");
    }
}
