<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/8/2019
 * Time: 10:24 PM
 */

use BackEnd\Routing\Request\SubCategory\DeleteSubCategory;
use PHPUnit\Framework\TestCase;

class DeleteSubCategoryTest extends TestCase
{
    /** @var \BackEnd\Database\DBSubCategories\DBSubCategories */
    private $subCategoryTable;
    private $category;
    public function setUp(){
        $this->category = array("category_id" => 5);
        $this->subCategoryTable = $this->getMockBuilder(\BackEnd\Database\DBSubCategories\DBSubCategories::class)->disableOriginalConstructor()
            ->setMethods(['deleteSubCategory', "doesSubCategoryExist"])->getMock();
    }

    public function test__construct(){
        $mandatoryFields = ["category_id"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->subCategoryTable, $request->getSubCategoryTable());
    }

    public function testGetResponse(){
        $request = $this->createRequest();
        $this->subCategoryTable->expects($this->once())
            ->method('deleteSubCategory')
            ->with($this->category["category_id"]);
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    public function testInitializationWithMissingParameters()
    {
        $this->category = array();
        $request = $this->createRequest();
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }

    protected function createRequest(){
        $deleteSubCategoryRequest = new DeleteSubCategory($this->subCategoryTable, $this->category);
        return $deleteSubCategoryRequest;
    }
}
