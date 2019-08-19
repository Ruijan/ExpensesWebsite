<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 7:29 PM
 */

use BackEnd\Routing\Request\Category\RetrieveAllCategories;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;
use BackEnd\Category;
use BackEnd\Database\DBCategories\DBCategories;

class RetrieveAllCategoriesTest extends ConnectedRequestTest
{
    protected $categoriesTable;
    protected $category;

    public function setUp()
    {
        parent::setUp();
        $this->category = $this->getMockBuilder(Category::class)->disableOriginalConstructor()
            ->setMethods(['asDict'])->getMock();
        $this->categoriesTable = $this->getMockBuilder(DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['getAllCategories'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->categoriesTable, $this->request->getCategoriesTable());
    }

    public function testExecute()
    {
        $category = array(
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
        $this->request = new RetrieveAllCategories($this->categoriesTable, $this->usersTable, $this->user, $this->data);
    }
}
