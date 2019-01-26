<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/16/2019
 * Time: 9:49 PM
 */
namespace BackEnd\Tests\Database\DBAccount;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBAccount\DBAccount;
use BackEnd\Database\DBAccount\AccountDuplicationException;
use BackEnd\Database\DBAccount\CurrencyIDException;
use BackEnd\Database\DBAccount\UserIDException;

class DBAccountTest extends TableCreationTest
{
    private $accountName = "Savings";
    private $currentAmount = 5431;
    private $userID = 11;
    private $currencyID = 2;
    private $usersTable;
    private $currencyTable;

    public function setUp(){
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUser::class)->disableOriginalConstructor()->setMethods(['checkIfIDExists'])->getMock();
        $this->currencyTable = $this->getMockBuilder(\BackEnd\Database\DBCurrency::class)->disableOriginalConstructor()->setMethods(['checkIfIDExists'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "USER_ID" => "int(11)",
            "ADDED_DATE" => "datetime",
            "CURRENCY_ID" => "int(11)",
            "CURRENT_AMOUNT" => "int(11)"];
        $this->name = "accounts";
    }

    public function createTable()
    {
        $this->table = new DBAccount($this->database, $this->usersTable, $this->currencyTable);
        $this->assertEquals($this->table->getUsersTable(), $this->usersTable);
        $this->assertEquals($this->table->getCurrenciesTable(), $this->currencyTable);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddAccount(){
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currencyTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(true));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->userID, $this->currencyID);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertEquals($this->accountName, $result["NAME"]);
        $this->assertEquals($this->currentAmount, $result["CURRENT_AMOUNT"]);
        $this->assertEquals($this->userID, $result["USER_ID"]);
    }

    public function testAddAccountWithWrongUserIDShouldThrow(){
        $success = false;
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(false));
        try{
            $this->table->addAccount($this->accountName, $this->currentAmount, $this->userID, $this->currencyID);
        }
        catch(UserIDException $e){
            $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
            $success = $result === NULL;
        }
        $this->assertTrue($success);
    }

    public function testAddAccountWithWrongCurrencyIDShouldThrow(){
        $success = false;
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currencyTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(false));
        try{
            $this->table->addAccount($this->accountName, $this->currentAmount, $this->userID, $this->currencyID);
        }
        catch(CurrencyIDException $e){
            $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
            $success = $result === NULL;
        }
        $this->assertTrue($success);
    }

    public function testAddAccountWithExistingName(){
        $this->usersTable->expects($this->exactly(2))
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currencyTable->expects($this->exactly(2))
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(true));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->userID, $this->currencyID);
        try{
            $this->table->addAccount($this->accountName, $this->currentAmount, $this->userID, $this->currencyID);
        }
        catch (AccountDuplicationException $e){
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
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currencyTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(true));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->userID, $this->currencyID);
        $isAlreadyInDB = $this->table->doesAccountExists($this->accountName, $this->userID);
        $this->assertTrue($isAlreadyInDB);
    }

    public function testIfAccountAlreadyExistsShouldReturnFalse(){
        $isAlreadyInDB = $this->table->doesAccountExists($this->accountName, $this->userID);
        $this->assertFalse($isAlreadyInDB);
    }
    
    public function testGetAccountsFromUserID(){
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currencyTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(true));
        $this->table->addAccount($this->accountName, $this->currentAmount, $this->userID, $this->currencyID);
        $accounts = $this->table->getAccountsFromUserID($this->userID);
        $this->assertEquals(1 , count($accounts));
        $this->assertEquals($this->accountName, $accounts[0]["NAME"]);
        $this->assertEquals($this->currentAmount, $accounts[0]["CURRENT_AMOUNT"]);
        $this->assertEquals($this->userID, $accounts[0]["USER_ID"]);
    }
}
