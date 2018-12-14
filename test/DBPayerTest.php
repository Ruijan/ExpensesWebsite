<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:13 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBPayer.php');
require_once("TableCreationTest.php");

class DBPayerTest extends TableCreationTest
{
    private $payer = ["FIRST_NAME" => "Julien",
        "NAME" => "Rechenmann",
        "EMAIL" => "jujudavid321@hotmail.com",
        "USERNAME" => "ruijan",
        "PASSWORD" => "admin",
        "REGISTERED_DATE" => "",
        "LAST_CONNECTION" => ""];
    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "FIRST_NAME" => "char(50)",
            "NAME" => "char(50)",
            "EMAIL" => "char(50)",
            "USERNAME" => "char(50)",
            "PASSWORD" => "char(50)",
            "REGISTERED_DATE" => "datetime",
            "LAST_CONNECTION" => "datetime"];
        $this->name = "payers";
        $this->payer["REGISTERED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->payer["REGISTERED_DATE"] = $this->payer["REGISTERED_DATE"]->format("Y-m-d H:i:s");
        $this->payer["LAST_CONNECTION"] = $this->payer["REGISTERED_DATE"];
    }

    public function createTable()
    {
        $this->table = new \src\DBPayer($this->database);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddPayer(){
        $this->table->addPayer($this->payer);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertArraySubset($this->payer, $result, true);
    }

    public function testAddPayerTwiceShouldThrow(){
        $this->table->addPayer($this->payer);
        try{
            $this->table->addPayer($this->payer);
        }
        catch (Exception $e){
            $count = 0;
            $result = $this->driver->query("SELECT * FROM ".$this->name);
            while($row = $result->fetch_assoc()){
                $this->assertArraySubset($this->payer, $row, true);
                $count += 1;
            }
            $this->assertEquals(1, $count);
            return;
        }
        $this->assertTrue(false);
    }

    public function testCheckIfPayerIDExist(){
        $this->table->addPayer($this->payer);
        $expectedPayerID = 1;
        $payerID = $this->table->checkIfPayerIDExists($expectedPayerID);
        $this->assertTrue($payerID);
    }

    public function testCheckIfPayerIDExistReturnFalse(){
        $this->table->addPayer($this->payer);
        $expectedPayerID = 2;
        $payerID = $this->table->checkIfPayerIDExists($expectedPayerID);
        $this->assertFalse($payerID);
    }

    public function testCheckIfPayerIDExistWithStringShouldThrow(){
        $this->table->addPayer($this->payer);
        $expectedPayerID = "Palalal";
        $this->expectException(\Exception::class);
        $this->table->checkIfPayerIDExists($expectedPayerID);
    }

    public function testCheckIfPayerEmailExist(){
        $this->table->addPayer($this->payer);
        $expectedPayerID = 1;
        $payerID = $this->table->checkIfPayerEmailExists($this->payer["EMAIL"]);
        $this->assertEquals($expectedPayerID, $payerID);
    }

    public function testCheckIfPayerEmailExistReturnFalse(){
        $this->table->addPayer($this->payer);
        $payerID = $this->table->checkIfPayerEmailExists("test@hotmail.com");
        $this->assertFalse($payerID);
    }
}
