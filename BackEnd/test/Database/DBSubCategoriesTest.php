<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:35 PM
 */

namespace BackEnd\Tests\Database;
use BackEnd\SubCategory;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use \BackEnd\Database\InsertionException;
use BackEnd\Database\DBUsers;
use BackEnd\Database\DBCategories;
use BackEnd\Database\DBSubCategories\UndefinedSubCategoryID;

class DBSubCategoriesTest extends TableCreationTest
{
    private $subCategoryArray = ["PARENT_ID" => "1", "NAME" => "Food", "USER_ID" => "1", "ADDED_DATE" => ""];
    private $subCategory;
    private $dbPayers;
    private $dbCategories;
    public function setUp(){
        $this->dbPayers = $this->getMockBuilder(DBUsers::class)->disableOriginalConstructor()->setMethods(['doesUserIDExist'])->getMock();
        $this->dbCategories = $this->getMockBuilder(DBCategories::class)->disableOriginalConstructor()->setMethods(['doesCategoryIDExist'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "PARENT_ID" => "int(11)",
            "NAME" => "char(50)",
            "USER_ID" => "int(11)",
            "ADDED_DATE" => "datetime"];
        $this->name = "sub_categories";
        $this->subCategoryArray["ADDED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->subCategoryArray["ADDED_DATE"] = $this->subCategoryArray["ADDED_DATE"]->format("Y-m-d H:i:s");
        $this->subCategory = $this->getMockBuilder(SubCategory::class)->disableOriginalConstructor()
            ->setMethods(['getName', 'getUserID', 'getParentID', 'getAddedDate'])->getMock();
    }

    public function createTable()
    {
        $this->table = new DBSubCategories($this->database, $this->dbPayers, $this->dbCategories);
        $this->assertEquals($this->table->getDBPayers(), $this->dbPayers);
        $this->assertEquals($this->table->getDBCategories(), $this->dbCategories);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddCategory(){
        $this->expectsSuccessfullSubCategoryInsertion();
        $this->table->addSubCategory($this->subCategory);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertArraySubset($this->subCategoryArray, $result, true);
    }

    public function testAddCategoryWithWrongPayerIDShouldThrow(){
        $this->subCategory->expects($this->exactly(1))->method("getUserID")
            ->will($this->returnValue($this->subCategoryArray["USER_ID"]));
        $this->dbPayers->expects($this->once())
            ->method('doesUserIDExist')->with($this->subCategoryArray["USER_ID"])->will($this->returnValue(false));
        try{
            $this->table->addSubCategory($this->subCategory);
        }
        catch (InsertionException $e){
            $this->checkNbRowHasBeenAdded(0);
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddCategoryWithWrongParentIDShouldThrow(){
        $expectedRows = 0;
        $currentRows = 1;
        $this->subCategory->expects($this->exactly(1))->method("getUserID")
            ->will($this->returnValue($this->subCategoryArray["USER_ID"]));
        $this->dbPayers->expects($this->once())
            ->method('doesUserIDExist')->with($this->subCategoryArray["USER_ID"])->will($this->returnValue(true));
        $this->subCategory->expects($this->exactly(1))->method("getParentID")
            ->will($this->returnValue($this->subCategoryArray["PARENT_ID"]));
        $this->dbCategories->expects($this->once())
            ->method('doesCategoryIDExist')->with($this->subCategoryArray["PARENT_ID"])->will($this->returnValue(false));
        try{
            $this->table->addSubCategory($this->subCategory);
        }
        catch (InsertionException $e){
            $this->checkNbRowHasBeenAdded($expectedRows);
            $currentRows = 0;
            return;
        }
        $this->assertEquals($expectedRows, $currentRows);
    }

    public function testAddCategoryTwiceShouldThrow(){
        $this->subCategory->expects($this->exactly(4))->method("getUserID")
            ->will($this->returnValue($this->subCategoryArray["USER_ID"]));
        $this->subCategory->expects($this->exactly(2))->method("getAddedDate")
            ->will($this->returnValue($this->subCategoryArray["ADDED_DATE"]));
        $this->subCategory->expects($this->exactly(4))->method("getParentID")
            ->will($this->returnValue($this->subCategoryArray["PARENT_ID"]));
        $this->subCategory->expects($this->exactly(2))->method("getName")
            ->will($this->returnValue($this->subCategoryArray["NAME"]));
        $this->dbPayers->expects($this->exactly(2))
            ->method('doesUserIDExist')->with($this->subCategoryArray["USER_ID"])->will($this->returnValue(true));
        $this->dbCategories->expects($this->exactly(2))
            ->method('doesCategoryIDExist')->with($this->subCategoryArray["USER_ID"])->will($this->returnValue(true));
        $this->table->addSubCategory($this->subCategory);
        try{
            $this->table->addSubCategory($this->subCategory);
        }
        catch (InsertionException $e){
            $this->checkNbRowHasBeenAdded(1);
            return;
        }
        $this->assertEquals(1, 2);
    }

    protected function checkNbRowHasBeenAdded($expectedNbRow): void
    {
        $count = 0;
        $result = $this->driver->query("SELECT * FROM " . $this->name);
        while ($row = $result->fetch_assoc()) {
            $this->assertArraySubset($this->subCategoryArray, $row, true);
            $count += 1;
        }
        $this->assertEquals($expectedNbRow, $count);
    }

    protected function expectsSuccessfullSubCategoryInsertion(): void
    {
        $this->subCategory->expects($this->exactly(2))->method("getUserID")
            ->will($this->returnValue($this->subCategoryArray["USER_ID"]));
        $this->subCategory->expects($this->exactly(1))->method("getAddedDate")
            ->will($this->returnValue($this->subCategoryArray["ADDED_DATE"]));
        $this->subCategory->expects($this->exactly(2))->method("getParentID")
            ->will($this->returnValue($this->subCategoryArray["PARENT_ID"]));
        $this->subCategory->expects($this->exactly(1))->method("getName")
            ->will($this->returnValue($this->subCategoryArray["NAME"]));
        $this->dbPayers->expects($this->exactly(1))
            ->method('doesUserIDExist')->with($this->subCategoryArray["USER_ID"])->will($this->returnValue(true));
        $this->dbCategories->expects($this->exactly(1))
            ->method('doesCategoryIDExist')->with($this->subCategoryArray["USER_ID"])->will($this->returnValue(true));
    }

    public function testGetSubCategoryFromID(){
        $this->expectsSuccessfullSubCategoryInsertion();
        $this->table->addSubCategory($this->subCategory);
        $subCategory = $this->table->getSubCategoryFromID(1);
        $this->assertEquals($this->subCategoryArray["NAME"], $subCategory["NAME"]);
    }

    public function testDeleteSubCategory(){
        $this->expectsSuccessfullSubCategoryInsertion();
        $this->table->addSubCategory($this->subCategory);
        $this->table->deleteSubCategory(1);
        $subCategory = $this->table->getSubCategoryFromID(1);
        $this->assertNull($subCategory);
    }

    public function testDeleteSubCategoryWithWrongIDShouldThrow(){
        $this->expectsSuccessfullSubCategoryInsertion();
        $this->table->addSubCategory($this->subCategory);
        $this->expectException(UndefinedSubCategoryID::class);
        $this->table->deleteSubCategory(2);
    }
}
