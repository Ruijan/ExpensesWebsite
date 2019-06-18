<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 9:49 AM
 */

use BackEnd\Routing\Request\Category\SubCategoryRequestFactory;
use PHPUnit\Framework\TestCase;
use \BackEnd\Routing\Request\Category\SubCategoryCreation;
use BackEnd\Database\DBTables;
use BackEnd\Database\Database;

class SubCategoryRequestFactoryTest extends TestCase
{
    private $database;

    public function setUp()
    {
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()
            ->setMethods(["getDriver", "getTableByName"])->getMock();    }

    public function testCreateCategoryCreationRequest()
    {
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::CATEGORIES], [DBTables::USERS]);
        $factory = new SubCategoryRequestFactory($this->database);
        $request = $factory->createRequest("Create");
        $this->assertEquals(SubCategoryCreation::class, get_class($request));
    }

    public function test__construct()
    {
        $factory = new SubCategoryRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new SubCategoryRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut");
    }
}
