<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:35 PM
 */

namespace BackEnd\Tests\Database;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBSubCategories;
use BackEnd\Database\DBUsers;
use BackEnd\Database\DBCategories;

class DBSubCategoriesTest extends TableCreationTest
{
    private $subCategory = ["PARENT_ID" => "1", "NAME" => "Food", "USER_ID" => "1", "ADDED_DATE" => ""];
    private $dbPayers;
    private $dbCategories;
    public function setUp(){
        $this->dbPayers = $this->getMockBuilder(DBUsers::class)->disableOriginalConstructor()->setMethods(['checkIfIDExists'])->getMock();
        $this->dbCategories = $this->getMockBuilder(DBCategories::class)->disableOriginalConstructor()->setMethods(['checkIfCategoryIDExists'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "PARENT_ID" => "int(11)",
            "NAME" => "char(50)",
            "USER_ID" => "int(11)",
            "ADDED_DATE" => "datetime"];
        $this->name = "sub_categories";
        $this->subCategory["ADDED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->subCategory["ADDED_DATE"] = $this->subCategory["ADDED_DATE"]->format("Y-m-d H:i:s");
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
        $this->dbPayers->expects($this->once())
            ->method('checkIfIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(true));
        $this->dbCategories->expects($this->once())
            ->method('checkIfCategoryIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(true));
        $this->table->addSubCategory($this->subCategory);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertArraySubset($this->subCategory, $result, true);
    }

    public function testAddCategoryWithWrongPayerIDShouldThrow(){
        $this->dbPayers->expects($this->once())
            ->method('checkIfIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(false));
        try{
            $this->table->addSubCategory($this->subCategory);
        }
        catch (\Exception $e){
            $this->checkNbRowHasBeenAdded(0);
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddCategoryWithWrongParentIDShouldThrow(){
        $expectedRows = 0;
        $currentRows = 1;
        $this->dbPayers->expects($this->once())
            ->method('checkIfIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(true));
        $this->dbCategories->expects($this->once())
            ->method('checkIfCategoryIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(false));
        try{
            $this->table->addSubCategory($this->subCategory);
        }
        catch (\Exception $e){
            $this->checkNbRowHasBeenAdded($expectedRows);
            $currentRows = 0;
            return;
        }
        $this->assertEquals($expectedRows, $currentRows);
    }

    public function testAddCategoryTwiceShouldThrow(){
        $this->dbPayers->expects($this->exactly(2))
            ->method('checkIfIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(true));
        $this->dbCategories->expects($this->exactly(2))
            ->method('checkIfCategoryIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(true));
        $this->table->addSubCategory($this->subCategory);
        try{
            $this->table->addSubCategory($this->subCategory);
        }
        catch (\Exception $e){
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
            $this->assertArraySubset($this->subCategory, $row, true);
            $count += 1;
        }
        $this->assertEquals($expectedNbRow, $count);
    }

    public function testGetSubCategoryFromID(){
        $this->dbPayers->expects($this->exactly(1))
            ->method('checkIfIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(true));
        $this->dbCategories->expects($this->exactly(1))
            ->method('checkIfCategoryIDExists')->with($this->subCategory["USER_ID"])->will($this->returnValue(true));
        $this->table->addSubCategory($this->subCategory);
        $subCategory = $this->table->getSubCategoryFromID(1);
        $this->assertEquals($this->subCategory["NAME"], $subCategory["NAME"]);
    }
}
