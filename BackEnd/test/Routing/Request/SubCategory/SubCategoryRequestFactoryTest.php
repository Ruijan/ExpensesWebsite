<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 9:49 AM
 */

use BackEnd\Routing\Request\SubCategory\SubCategoryRequestFactory;
use PHPUnit\Framework\TestCase;
use \BackEnd\Routing\Request\SubCategory\SubCategoryCreation;
use BackEnd\Database\DBTables;
use BackEnd\Database\Database;

class SubCategoryRequestFactoryTest extends TestCase
{
    private $database;

    public function setUp()
    {
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()
            ->setMethods(["getDriver", "getTableByName"])->getMock();
    }

    public function testCreateCategoryCreationRequest()
    {
        $this->database->expects($this->exactly(3))
            ->method('getTableByName')
            ->withConsecutive([DBTables::SUBCATEGORIES], [DBTables::CATEGORIES], [DBTables::USERS]);
        $factory = new SubCategoryRequestFactory($this->database);
        $request = $factory->createRequest("Create");
        $this->assertEquals(SubCategoryCreation::class, get_class($request));
    }


    public function testCreateRetrieveAllCategoriesRequest()
    {
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::SUBCATEGORIES], [DBTables::USERS]);
        $factory = new SubCategoryRequestFactory($this->database);
        $request = $factory->createRequest("RetrieveAll");
        $this->assertEquals(\BackEnd\Routing\Request\SubCategory\RetrieveAllSubCategories::class, get_class($request));
    }

    public function testCreateDeleteSubCategoryRequest()
    {
        $this->database->expects($this->exactly(1))
            ->method('getTableByName')
            ->with(DBTables::SUBCATEGORIES);
        $factory = new SubCategoryRequestFactory($this->database);
        $request = $factory->createRequest("Delete");
        $this->assertEquals(\BackEnd\Routing\Request\SubCategory\DeleteSubCategory::class, get_class($request));
    }


    public function test__construct()
    {
        $factory = new SubCategoryRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateWrongTypeOfRequestShouldThrow()
    {
        $factory = new SubCategoryRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut");
    }
}
