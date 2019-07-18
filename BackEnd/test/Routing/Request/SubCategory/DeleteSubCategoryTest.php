<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/8/2019
 * Time: 10:24 PM
 */

use BackEnd\Routing\Request\SubCategory\DeleteSubCategory;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class DeleteSubCategoryTest extends ConnectedRequestTest
{
    /** @var \BackEnd\Database\DBSubCategories\DBSubCategories */
    private $subCategoryTable;
    public function setUp(){
        $this->data = array("category_id" => 5);
        parent::setUp();
        $this->subCategoryTable = $this->getMockBuilder(\BackEnd\Database\DBSubCategories\DBSubCategories::class)->disableOriginalConstructor()
            ->setMethods(['deleteSubCategory', "doesSubCategoryExist"])->getMock();
    }

    public function test__construct(){
        $this->mandatoryFields[] = "category_id";
        parent::test__construct();
        $this->assertEquals($this->subCategoryTable, $this->request->getSubCategoryTable());
    }

    public function testGetResponse(){
        $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $this->user->expects($this->once())
            ->method('connectWithSessionID')
            ->with($this->usersTable, $this->data["session_id"], $this->data["user_id"]);
        $this->subCategoryTable->expects($this->once())
            ->method('deleteSubCategory')
            ->with($this->data["category_id"]);
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    protected function createRequest(){
        $this->request = new DeleteSubCategory($this->subCategoryTable, $this->usersTable, $this->user, $this->data);
    }
}
