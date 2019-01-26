<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:35 PM
 */

namespace BackEnd\Tests\Database;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBUsers;
use BackEnd\Database\DBCategories;

class DBCategoryTest extends TableCreationTest
{
    private $usersTable;
    private $category = ["NAME" => "Food", "USER_ID" => "1", "ADDED_DATE" => ""];
    public function setUp(){
        $this->usersTable = $this->getMockBuilder(DBUsers::class)->disableOriginalConstructor()->setMethods(['checkIfIDExists'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "USER_ID" => "int(11)",
            "ADDED_DATE" => "datetime"];
        $this->name = "categories";
        $this->category["ADDED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->category["ADDED_DATE"] = $this->category["ADDED_DATE"]->format("Y-m-d H:i:s");
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
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->category["USER_ID"])->will($this->returnValue(true));
        $this->table->addCategory($this->category);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertArraySubset($this->category, $result, true);
    }

    public function testAddCategoryWithWrongPayerIDShouldThrow(){
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->category["USER_ID"])->will($this->returnValue(false));
        try{
            $this->table->addCategory($this->category);
        }
        catch (\Exception $e){
            $this->checkNbRowHasBeenAdded(0);
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddCategoryTwiceShouldThrow(){
        $this->usersTable->expects($this->exactly(2))
            ->method('checkIfIDExists')->with($this->category["USER_ID"])->will($this->returnValue(true));
        $this->table->addCategory($this->category);
        try{
            $this->table->addCategory($this->category);
        }
        catch (\Exception $e){
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
            $this->assertArraySubset($this->category, $row, true);
            $count += 1;
        }
        $this->assertEquals($expectedNbRow, $count);
    }
}
