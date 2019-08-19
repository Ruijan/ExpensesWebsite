<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:35 PM
 */

namespace BackEnd\Tests\Database;
use BackEnd\Category;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\InsertionException;

class DBCategoriesTest extends TableCreationTest
{
    private $usersTable;
    private $category;
    private $categoryDict = ["NAME" => "Food", "USER_ID" => "1", "ADDED_DATE" => ""];
    public function setUp(){
        $this->usersTable = $this->getMockBuilder(DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['doesUserIDExist'])->getMock();
        $this->category = $this->getMockBuilder(Category::class)->disableOriginalConstructor()
            ->setMethods(['getUserID', 'getName', 'getAddedDate', 'asDict'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "USER_ID" => "int(11)",
            "ADDED_DATE" => "datetime"];
        $this->name = "categories";
        $this->categoryDict["ADDED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->categoryDict["ADDED_DATE"] = $this->categoryDict["ADDED_DATE"]->format("Y-m-d H:i:s");
    }

    public function createTable()
    {
        $this->table = new DBCategories($this->database, $this->usersTable);
        $this->assertEquals($this->table->getUsersTable(), $this->usersTable);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddCategory(){
        $this->expectsSuccessfullCategoryInsertion();
        $this->usersTable->expects($this->once())
            ->method('doesUserIDExist')->with($this->categoryDict["USER_ID"])->will($this->returnValue(true));
        $this->table->addCategory($this->category);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertArraySubset($this->categoryDict, $result, true);
    }

    public function testAddCategoryWithWrongPayerIDShouldThrow(){
        $this->category->expects($this->exactly(1))->method("getUserID")
            ->will($this->returnValue($this->categoryDict["USER_ID"]));
        $this->category->expects($this->once())->method("asDict")
            ->will($this->returnValue($this->categoryDict));
        $this->usersTable->expects($this->once())
            ->method('doesUserIDExist')->with($this->categoryDict["USER_ID"])->will($this->returnValue(false));
        try{
            $this->table->addCategory($this->category);
        }
        catch (InsertionException $e){
            $this->checkNbRowHasBeenAdded(0);
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddCategoryTwiceShouldThrow(){
        $this->category->expects($this->exactly(4))->method("getUserID")
            ->will($this->returnValue($this->categoryDict["USER_ID"]));
        $this->category->expects($this->exactly(1))->method("asDict")
            ->will($this->returnValue($this->categoryDict));
        $this->category->expects($this->exactly(2))->method("getAddedDate")
            ->will($this->returnValue($this->categoryDict["ADDED_DATE"]));
        $this->category->expects($this->exactly(2))->method("getName")
            ->will($this->returnValue($this->categoryDict["NAME"]));
        $this->usersTable->expects($this->exactly(2))
            ->method('doesUserIDExist')->with($this->categoryDict["USER_ID"])->will($this->returnValue(true));
        $this->table->addCategory($this->category);
        try{
            $this->table->addCategory($this->category);
        }
        catch (InsertionException $e){
            $this->checkNbRowHasBeenAdded(1);
            return;
        }
        $this->assertTrue(false);
    }

    protected function checkNbRowHasBeenAdded($expectedNbRow): void
    {
        $count = 0;
        $result = $this->driver->query("SELECT * FROM " . $this->name);
        while ($row = $result->fetch_assoc()) {
            $this->assertArraySubset($this->categoryDict, $row, true);
            $count += 1;
        }
        $this->assertEquals($expectedNbRow, $count);
    }

    public function testGetCategoryFromID(){
        $this->expectsSuccessfullCategoryInsertion();
        $this->usersTable->expects($this->once())
            ->method('doesUserIDExist')->with($this->categoryDict["USER_ID"])->will($this->returnValue(true));
        $this->table->addCategory($this->category);
        $category = $this->table->getCategoryFromID(1);
        $this->assertEquals($this->categoryDict["NAME"] , $category["NAME"]);
    }

    public function testGetAllCategories(){
        $this->expectsSuccessfullCategoryInsertion();
        $this->usersTable->expects($this->once())
            ->method('doesUserIDExist')->with($this->categoryDict["USER_ID"])->will($this->returnValue(true));
        $this->table->addCategory($this->category);
        $categories = $this->table->getAllCategories();
        $this->assertEquals($this->categoryDict["NAME"] , $categories[0]->getName());
    }

    protected function expectsSuccessfullCategoryInsertion(): void
    {
        $this->category->expects($this->exactly(2))->method("getUserID")
            ->will($this->returnValue($this->categoryDict["USER_ID"]));
        $this->category->expects($this->exactly(1))->method("getAddedDate")
            ->will($this->returnValue($this->categoryDict["ADDED_DATE"]));
        $this->category->expects($this->exactly(1))->method("getName")
            ->will($this->returnValue($this->categoryDict["NAME"]));
    }
}
