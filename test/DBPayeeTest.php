<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:13 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBPayee.php');
require_once("TableCreationTest.php");

class DBPayeeTest extends TableCreationTest
{

    private $payeeName = "Migros";
    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "ADDED_DATE" => "datetime"];
        $this->name = "payees";
    }

    public function createTable()
    {
        try{
            $this->table = new \src\DBPayee($this->database);
        }
        catch(Exception $e){
        }

    }

    public function testAddPayee(){
        $this->table->addPayee($this->payeeName);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertEquals($this->payeeName, $result["NAME"]);
    }

    public function testAddPayeeTwiceShouldThrow(){
        $this->table->addPayee($this->payeeName);
        try{
            $this->table->addPayee($this->payeeName);
        }
        catch (Exception $e){
            $count = 0;
            $result = $this->driver->query("SELECT * FROM ".$this->name);
            while($row = $result->fetch_assoc()){
                $this->assertEquals($this->payeeName, $row["NAME"]);
                $count += 1;
            }
            $this->assertEquals(1, $count);
            return;
        }
        $this->assertTrue(false);
    }

    public function testCheckIfPayeeIDExist(){
        $this->table->addPayee($this->payeeName);
        $expectedPayeeID = 1;
        $payeeID = $this->table->checkIfPayeeExists($expectedPayeeID);
        $this->assertEquals($expectedPayeeID, $payeeID);
    }

    public function testCheckIfPayeeIDExistReturnFalse(){
        $this->table->addPayee($this->payeeName);
        $expectedPayeeID = 2;
        $payeeID = $this->table->checkIfPayeeExists($expectedPayeeID);
        $this->assertFalse($payeeID);
    }

    public function testCheckIfPayeeIDExistWithStringShouldThrow(){
        $this->table->addPayee($this->payeeName);
        $expectedPayeeID = "Palalal";
        $this->expectException(\Exception::class);
        $this->table->checkIfPayeeExists($expectedPayeeID);
    }
}
