<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 7:29 PM
 */

use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Routing\Request\Category\RetrieveAllCategories;
use BackEnd\Routing\Request\Category\RetrieveAllTree;
use BackEnd\SubCategory;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use BackEnd\Category;
use BackEnd\Database\DBCategories\DBCategories;

class RetrieveTreeTest extends ConnectedRequestTest
{
    protected $categoriesTable;
    protected $subCategoriesTable;
    protected $category;
    protected $subCategory;

    public function setUp()
    {
        parent::setUp();
        $this->category = $this->getMockBuilder(Category::class)->disableOriginalConstructor()
            ->setMethods(['asDict'])->getMock();
        $this->subCategory = $this->getMockBuilder(SubCategory::class)->disableOriginalConstructor()
            ->setMethods(['asDict'])->getMock();
        $this->categoriesTable = $this->getMockBuilder(DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['getAllCategories'])->getMock();
        $this->subCategoriesTable = $this->getMockBuilder(DBSubCategories::class)->disableOriginalConstructor()
            ->setMethods(['getAllSubCategories'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->categoriesTable, $this->request->getCategoriesTable());
        $this->assertEquals($this->subCategoriesTable, $this->request->getSubCategoriesTable());
    }

    public function testExecute()
    {
        $category = array(
            "name" => "Food",
            "user_id" => 2,
            "added_date" => "2019-06-12 00:00:00"
        );
        $subCategory = array(
            "name" => "Food",
            "user_id" => 2,
            "added_date" => "2019-06-12 00:00:00"
        );
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->category->expects($this->once())
            ->method('asDict')
            ->with()->will($this->returnValue($category));
        $this->categoriesTable->expects($this->once())
            ->method('getAllCategories')
            ->with()->will($this->returnValue(array($this->category)));
        $this->subCategory->expects($this->once())
            ->method('asDict')
            ->with()->will($this->returnValue($subCategory));
        $this->subCategoriesTable->expects($this->once())
            ->method('getAllSubCategories')
            ->with()->will($this->returnValue(array($this->subCategory)));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        if ($response["STATUS"] == "ERROR") {
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        } else {
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    protected function createRequest()
    {
        $this->request = new RetrieveAllTree($this->categoriesTable, $this->subCategoriesTable,$this->usersTable, $this->user, $this->data);
    }
}
