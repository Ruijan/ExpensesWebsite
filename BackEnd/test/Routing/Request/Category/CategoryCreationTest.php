<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/15/2019
 * Time: 12:42 PM
 */

use BackEnd\Routing\Request\Category\CategoryCreation;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class CategoryCreationTest extends ConnectedRequestTest
{
    private $categoriesTable;

    public function setUp()
    {
        $this->data = array("name" => "Food");
        parent::setUp();
        $this->mandatoryFields[] = "name";
        $this->categoriesTable = $this->getMockBuilder(\BackEnd\Database\DBCategories\DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['addCategory'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->categoriesTable, $this->request->getCategoriesTable());
    }

    public function testExecute()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->categoriesTable->expects($this->once())
            ->method('addCategory');
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    protected function createRequest()
    {
        $this->request = new CategoryCreation($this->categoriesTable, $this->usersTable, $this->user, $this->data);
    }

}
