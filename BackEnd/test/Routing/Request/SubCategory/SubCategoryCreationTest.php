<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/15/2019
 * Time: 12:42 PM
 */

use BackEnd\Routing\Request\SubCategory\SubCategoryCreation;
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class SubCategoryCreationTest extends ConnectedRequestTest
{
    private $categoriesTable;
    private $subCategoriesTable;

    public function setUp()
    {
        $this->data = array("name" => "Food",
            "parent_id" => 1);
        parent::setUp();
        $this->categoriesTable = $this->getMockBuilder(\BackEnd\Database\DBCategories\DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['addCategory'])->getMock();
        $this->subCategoriesTable = $this->getMockBuilder(\BackEnd\Database\DBCategories\DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['addSubCategory'])->getMock();
    }

    public function test__construct()
    {
        $this->mandatoryFields = array_merge($this->mandatoryFields, ["name", "parent_id"]);
        parent::test__construct();
        $this->assertEquals($this->categoriesTable, $this->request->getCategoriesTable());
        $this->assertEquals($this->subCategoriesTable, $this->request->getSubCategoriesTable());
    }

    public function testExecute()
    {
        $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $this->user->expects($this->once())
            ->method('connectWithSessionID')
            ->with($this->usersTable, $this->data["session_id"], $this->data["user_id"]);
        $this->subCategoriesTable->expects($this->once())
            ->method('addSubCategory');
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    protected function createRequest()
    {
        $this->request = new SubCategoryCreation($this->subCategoriesTable, $this->categoriesTable, $this->usersTable, $this->user, $this->data);
    }

}
