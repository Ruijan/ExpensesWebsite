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
use \BackEnd\Routing\Request\Currency\DeleteCurrency;
use \BackEnd\Routing\Request\Currency\RetrieveAllCurrencies;

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
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::CURRENCIES], [DBTables::USERS]);
        $factory = new CurrencyRequestFactory($this->database);
        $request = $factory->createRequest("Create");
        $this->assertEquals(CurrencyCreation::class, get_class($request));
    }
    public function testCreateCurrencyDeleteRequest(){
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::CURRENCIES], [DBTables::USERS]);
        $factory = new CurrencyRequestFactory($this->database);
        $request = $factory->createRequest("Delete");
        $this->assertEquals(DeleteCurrency::class, get_class($request));
    }

    public function testCreateRetrieveAllRequest(){
        $this->database->expects($this->exactly(1))
            ->method('getTableByName')
            ->with(DBTables::CURRENCIES);
        $factory = new CurrencyRequestFactory($this->database);
        $request = $factory->createRequest("RetrieveAll");
        $this->assertEquals(RetrieveAllCurrencies::class, get_class($request));
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new CurrencyRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $factory->createRequest("Tutut");
    }
}
