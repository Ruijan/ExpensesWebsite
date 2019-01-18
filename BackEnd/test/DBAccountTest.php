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
    private $payee_id = 11;
    private $dbPayees;

    public function setUp(){
        $this->dbPayees = $this->getMockBuilder(\src\DBPayer::class)->disableOriginalConstructor()->setMethods(['checkIfPayeeIDExists'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "PAYEE_ID" => "int(11)",
            "ADDED_DATE" => "datetime",
            "CURRENT_AMOUNT" => "int(11)"];
        $this->name = "accounts";
    }

    public function createTable()
    {
        $this->table = new \src\DBAccount($this->database, $this->dbPayees);
        $this->assertEquals($this->table->getDBPayees(), $this->dbPayees);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddAccount(){
        $this->dbPayees->expects($this->once())
            ->method('checkIfPayeeIDExists')->with($this->payee_id)->will($this->returnValue(false));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->payee_id);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertEquals($this->accountName, $result["NAME"]);
        $this->assertEquals($this->currentAmount, $result["CURRENT_AMOUNT"]);
        $this->assertEquals($this->payee_id, $result["PAYEE_ID"]);
    }

    public function testAddAccountWithExistingName(){
        $this->dbPayees->expects($this->exactly(2))
            ->method('checkIfPayeeIDExists')->with($this->payee_id)->will($this->returnValue(false));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->payee_id);
        try{
            $this->table->addAccount($this->accountName, $this->currentAmount, $this->payee_id);
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
        $this->dbPayees->expects($this->once())
            ->method('checkIfPayeeIDExists')->with($this->payee_id)->will($this->returnValue(false));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->payee_id);
        $isAlreadyInDB = $this->table->doesAccountExists($this->accountName, $this->payee_id);
        $this->assertTrue($isAlreadyInDB);
    }

    public function testIfAccountAlreadyExistsShouldReturnFalse(){
        $isAlreadyInDB = $this->table->doesAccountExists($this->accountName, $this->payee_id);
        $this->assertFalse($isAlreadyInDB);
    }
}
