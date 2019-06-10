<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 6:47 PM
 */

use BackEnd\Routing\Request\Currency\CurrencyRequestFactory;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\Currency\CurrencyCreation;

class CurrencyRequestFactoryTest extends TestCase
{
    private $database;

    public function setUp()
    {
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()
            ->setMethods(["getDriver", "getTableByName"])->getMock();
    }

    public function test__construct()
    {
        $factory = new CurrencyRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateCurrencyCreationRequest(){
        $this->database->expects($this->once())
            ->method('getTableByName')
            ->with(DBTables::CURRENCIES);
        $factory = new CurrencyRequestFactory($this->database);
        $request = $factory->createRequest("Create");
        $this->assertEquals(CurrencyCreation::class, get_class($request));
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new CurrencyRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $factory->createRequest("Tutut");
    }
}
