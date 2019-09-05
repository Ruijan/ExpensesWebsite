<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/15/2019
 * Time: 12:42 PM
 */

use BackEnd\Routing\Request\Category\DeleteCategory;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class DeleteCategoryTest extends ConnectedRequestTest
{
    private $categoriesTable;

    public function setUp()
    {
        $this->data = array("category_id" => 1);
        parent::setUp();
        $this->mandatoryFields[] = "category_id";
        $this->categoriesTable = $this->getMockBuilder(\BackEnd\Database\DBCategories\DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['deleteCategory'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->categoriesTable, $this->request->getCategoryTable());
    }

    public function testExecute()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->categoriesTable->expects($this->once())
            ->method('deleteCategory')->with($this->data["category_id"]);
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    /**
     * @return \BackEnd\Routing\Request\Request|void
     */
    protected function createRequest()
    {
        $this->request = new DeleteCategory($this->categoriesTable, $this->usersTable, $this->user, $this->data);
    }

}
