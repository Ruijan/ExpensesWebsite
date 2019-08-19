<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 7:29 PM
 */

use BackEnd\Routing\Request\SubCategory\RetrieveAllSubCategories;
use BackEnd\SubCategory;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class RetrieveAllSubCategoriesTest extends ConnectedRequestTest
{
    protected $subCategoriesTable;
    protected $data;
    protected $subCategory;

    public function setUp(){
        parent::setUp();
        $this->subCategory = $this->getMockBuilder(SubCategory::class)->disableOriginalConstructor()
            ->setMethods(['asDict'])->getMock();
        $this->subCategoriesTable = $this->getMockBuilder(DBSubCategories::class)->disableOriginalConstructor()
            ->setMethods(['getAllSubCategories'])->getMock();
    }

    public function test__construct(){
        parent::test__construct();
        $this->assertEquals($this->subCategoriesTable, $this->request->getSubCategoriesTable());

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
        $this->subCategory->expects($this->once())
            ->method('asDict')
            ->with()->will($this->returnValue($category));
        $this->subCategoriesTable->expects($this->once())
            ->method('getAllSubCategories')
            ->with()->will($this->returnValue(array($this->subCategory)));
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
        $this->request = new RetrieveAllSubCategories($this->subCategoriesTable, $this->usersTable, $this->user, $this->data);
    }
}
