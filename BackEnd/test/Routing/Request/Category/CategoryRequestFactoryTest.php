<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 9:49 AM
 */

use BackEnd\Routing\Request\Category\CategoryRequestFactory;
use PHPUnit\Framework\TestCase;
use \BackEnd\Routing\Request\Category\CategoryCreation;
use BackEnd\Database\DBTables;
use BackEnd\Database\Database;

class CategoryRequestFactoryTest extends TestCase
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
        $factory = new CategoryRequestFactory($this->database);
        $request = $factory->createRequest("Create");
        $this->assertEquals(CategoryCreation::class, get_class($request));
    }

    public function testCreateRetrieveAllCategoriesRequest()
    {
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::CATEGORIES], [DBTables::USERS]);
        $factory = new CategoryRequestFactory($this->database);
        $request = $factory->createRequest("RetrieveAll");
        $this->assertEquals(\BackEnd\Routing\Request\Category\RetrieveAllCategories::class, get_class($request));
    }

    public function test__construct()
    {
        $factory = new CategoryRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new CategoryRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut");
    }
}
