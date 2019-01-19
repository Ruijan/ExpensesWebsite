<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/16/2019
 * Time: 9:49 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBAccount.php');
require_once("TableCreationTest.php");

class DBAccountTest extends TableCreationTest
{
    private $accountName = "Savings";
    private $currentAmount = 5431;
    private $payerID = 11;
    private $dbPayers;

    public function setUp(){
        $this->dbPayers = $this->getMockBuilder(\src\DBPayer::class)->disableOriginalConstructor()->setMethods(['checkIfPayerIDExists'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "PAYER_ID" => "int(11)",
            "ADDED_DATE" => "datetime",
            "CURRENT_AMOUNT" => "int(11)"];
        $this->name = "accounts";
    }

    public function createTable()
    {
        $this->table = new \src\DBAccount($this->database, $this->dbPayers);
        $this->assertEquals($this->table->getDBPayers(), $this->dbPayers);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddAccount(){
        $this->dbPayers->expects($this->once())
            ->method('checkIfPayerIDExists')->with($this->payerID)->will($this->returnValue(false));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->payerID);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertEquals($this->accountName, $result["NAME"]);
        $this->assertEquals($this->currentAmount, $result["CURRENT_AMOUNT"]);
        $this->assertEquals($this->payerID, $result["PAYER_ID"]);
    }

    public function testAddAccountWithWrongPayerIDShouldThrow(){
        $success = false;
        $this->dbPayers->expects($this->once())
            ->method('checkIfPayerIDExists')->with($this->payerID)->will($this->returnValue(true));
        try{
            $this->table->addAccount($this->accountName, $this->currentAmount, $this->payerID);
        }
        catch(\Exception $e){
            $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
            $success = $result === NULL;
        }
        $this->assertTrue($success);
    }

    public function testAddAccountWithExistingName(){
        $this->dbPayers->expects($this->exactly(2))
            ->method('checkIfPayerIDExists')->with($this->payerID)->will($this->returnValue(false));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->payerID);
        try{
            $this->table->addAccount($this->accountName, $this->currentAmount, $this->payerID);
        }
        catch (Exception $e){
            $count = 0;
            $result = $this->driver->query("SELECT * FROM ".$this->name);
            while($row = $result->fetch_assoc()){
                $this->assertEquals($this->accountName, $row["NAME"]);
                $count += 1;
            }
            $this->assertEquals(1, $count);
            return;
        }
        $this->assertTrue(false);
    }

    public function testIfAccountAlreadyExistsShouldReturnTrue(){
        $this->dbPayers->expects($this->once())
            ->method('checkIfPayerIDExists')->with($this->payerID)->will($this->returnValue(false));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->payerID);
        $isAlreadyInDB = $this->table->doesAccountExists($this->accountName, $this->payerID);
        $this->assertTrue($isAlreadyInDB);
    }

    public function testIfAccountAlreadyExistsShouldReturnFalse(){
        $isAlreadyInDB = $this->table->doesAccountExists($this->accountName, $this->payerID);
        $this->assertFalse($isAlreadyInDB);
    }
}
