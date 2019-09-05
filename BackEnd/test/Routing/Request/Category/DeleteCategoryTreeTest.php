<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/15/2019
 * Time: 12:42 PM
 */

use BackEnd\Routing\Request\Category\CategoryCreation;
use BackEnd\Routing\Request\Category\DeleteCategoryTree;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class DeleteCategoryTreeTest extends ConnectedRequestTest
{
    private $categoriesTable;
    private $subCategoriesTable;

    public function setUp()
    {
        $this->data = array("category_id" => 1);
        parent::setUp();
        $this->mandatoryFields[] = "category_id";
        $this->categoriesTable = $this->getMockBuilder(\BackEnd\Database\DBCategories\DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['deleteCategory'])->getMock();
        $this->subCategoriesTable = $this->getMockBuilder(\BackEnd\Database\DBSubCategories\DBSubCategories::class)->disableOriginalConstructor()
            ->setMethods(['deleteSubCategoryFromParentID'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->categoriesTable, $this->request->getCategoryTable());
        $this->assertEquals($this->subCategoriesTable, $this->request->getSubCategoryTable());
    }

    public function testExecute()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->categoriesTable->expects($this->once())
            ->method('deleteCategory')->with($this->data["category_id"]);
        $this->subCategoriesTable->expects($this->once())
            ->method('deleteSubCategoryFromParentID')->with($this->data["category_id"]);
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    protected function createRequest()
    {
        $this->request = new DeleteCategoryTree($this->categoriesTable, $this->subCategoriesTable, $this->usersTable, $this->user, $this->data);
    }

}
