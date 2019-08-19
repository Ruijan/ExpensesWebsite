<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 12:24 PM
 */

use BackEnd\Routing\Request\Payee\PayeeRequestFactory;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\DBTables;
use BackEnd\Database\Database;

class PayeeRequestFactoryTest extends TestCase
{
    private $database;

    public function setUp()
    {
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()
            ->setMethods(["getDriver", "getTableByName"])->getMock();    }

    public function testCreateExpenseStateCreationRequest()
    {
        $factory = $this->createSuccessfulFactory();
        $request = $factory->createRequest("Create", array());
        $this->assertEquals(\BackEnd\Routing\Request\Payee\PayeeCreation::class, get_class($request));
    }

    public function testCreateRetrieveAllExpenseStatesRequest()
    {
        $factory = $this->createSuccessfulFactory();
        $request = $factory->createRequest("RetrieveAll", array());
        $this->assertEquals(\BackEnd\Routing\Request\Payee\RetrieveAllPayees::class, get_class($request));
    }

    public function testCreateDeleteExpenseStateRequest()
    {
        $factory = $this->createSuccessfulFactory();
        $request = $factory->createRequest("Delete", array());
        $this->assertEquals(\BackEnd\Routing\Request\Payee\DeletePayee::class, get_class($request));
    }

    public function test__construct()
    {
        $factory = new PayeeRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new PayeeRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut", array());
    }

    /**
     * @return PayeeRequestFactory
     */
    protected function createSuccessfulFactory(): PayeeRequestFactory
    {
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::PAYEES], [DBTables::USERS]);
        return new PayeeRequestFactory($this->database);
    }
}
