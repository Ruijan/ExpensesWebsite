<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:35 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBCategories.php');
require_once("TableCreationTest.php");

class DBCategoryTest extends TableCreationTest
{
    private $dbPayers;
    private $category = ["NAME" => "Food", "PAYER_ID" => "1", "ADDED_DATE" => ""];
    public function setUp(){
        $this->dbPayers = $this->getMockBuilder(\src\DBPayer::class)->disableOriginalConstructor()->setMethods(['checkIfPayerIDExists'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "PAYER_ID" => "int(11)",
            "ADDED_DATE" => "datetime"];
        $this->name = "categories";
        $this->category["ADDED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->category["ADDED_DATE"] = $this->category["ADDED_DATE"]->format("Y-m-d H:i:s");
    }

    public function createTable()
    {
        $this->table = new \src\DBCategories($this->database, $this->dbPayers);
        $this->assertEquals($this->table->getDBPayers(), $this->dbPayers);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddCategory(){
        $this->dbPayers->expects($this->once())
            ->method('checkIfPayerIDExists')->with($this->category["PAYER_ID"])->will($this->returnValue($this->category["PAYER_ID"]));
        $this->table->addCategory($this->category);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertArraySubset($this->category, $result, true);
    }

    public function testAddCategoryWithWrongPayerIDShouldThrow(){
        $this->dbPayers->expects($this->once())
            ->method('checkIfPayerIDExists')->with($this->category["PAYER_ID"])->will($this->returnValue(2));
        try{
            $this->table->addCategory($this->category);
        }
        catch (Exception $e){
            $this->checkNbRowHasBeenAdded(0);
            return;
        }
        $this->assertTrue(false);
    }

    public function testAddCategoryTwiceShouldThrow(){
        $this->dbPayers->expects($this->exactly(2))
            ->method('checkIfPayerIDExists')->with($this->category["PAYER_ID"])->will($this->returnValue($this->category["PAYER_ID"]));
        $this->table->addCategory($this->category);
        try{
            $this->table->addCategory($this->category);
        }
        catch (Exception $e){
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
