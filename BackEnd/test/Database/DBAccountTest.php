<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/16/2019
 * Time: 9:49 PM
 */

namespace BackEnd\Tests\Database\DBAccount;

use BackEnd\Account\Account;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBAccounts\DBAccounts;
use BackEnd\Database\DBAccounts\AccountDuplicationException;
use BackEnd\Database\DBAccounts\CurrencyIDException;
use BackEnd\Database\DBAccounts\UserIDException;

class DBAccountTest extends TableCreationTest
{
    private $accountName = "Savings";
    private $currentAmount = 5431;
    private $userID = 11;
    private $currencyID = 2;
    private $usersTable;
    private $currenciesTable;
    private $account;

    public function setUp()
    {
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers::class)
            ->disableOriginalConstructor()->setMethods(['checkIfIDExists', 'getUserFromID'])->getMock();
        $this->currenciesTable = $this->getMockBuilder(\BackEnd\Database\DBCurrencies::class)
            ->disableOriginalConstructor()->setMethods(['checkIfIDExists', 'getCurrencyFromID'])->getMock();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "USER_ID" => "int(11)",
            "ADDED_DATE" => "datetime",
            "CURRENCY_ID" => "int(11)",
            "CURRENT_AMOUNT" => "int(11)"];
        $this->name = "accounts";
        $currentUTCDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->account = new Account(["name" => "Savings", "current_amount" => 5431, "user_id" => 11,
            "currency" => "CHF", "currency_id" => 2, "added_date" => $currentUTCDate->format("Y-m-d H:i:s")]);
    }

    public function createTable()
    {
        $this->table = new DBAccounts($this->database, $this->usersTable, $this->currenciesTable);
        $this->assertEquals($this->table->getUsersTable(), $this->usersTable);
        $this->assertEquals($this->table->getCurrenciesTable(), $this->currenciesTable);
    }

    public function initTable()
    {
        $this->table->init();
    }

    public function testAddAccount()
    {
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currenciesTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(true));
        $this->table->addAccount($this->account);
        $result = $this->driver->query("SELECT * FROM " . $this->name)->fetch_assoc();
        $this->assertEquals($this->accountName, $result["NAME"]);
        $this->assertEquals($this->currentAmount, $result["CURRENT_AMOUNT"]);
        $this->assertEquals($this->userID, $result["USER_ID"]);
        $this->assertEquals(1, $this->account->getTableID());
    }

    public function testAddAccountWithWrongUserIDShouldThrow()
    {
        $success = false;
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(false));
        try {
            $this->table->addAccount($this->account);
        } catch (UserIDException $e) {
            $result = $this->driver->query("SELECT * FROM " . $this->name)->fetch_assoc();
            $success = $result === NULL;
        }
        $this->assertTrue($success);
    }

    public function testAddAccountWithWrongCurrencyIDShouldThrow()
    {
        $success = false;
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currenciesTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(false));
        try {
            $this->table->addAccount($this->account);
        } catch (CurrencyIDException $e) {
            $result = $this->driver->query("SELECT * FROM " . $this->name)->fetch_assoc();
            $success = $result === NULL;
        }
        $this->assertTrue($success);
    }

    public function testAddAccountWithExistingName()
    {
        $this->usersTable->expects($this->exactly(2))
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currenciesTable->expects($this->exactly(2))
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(true));
        $this->table->addAccount($this->account);
        try {
            $this->table->addAccount($this->account);
        } catch (AccountDuplicationException $e) {
            $count = 0;
            $result = $this->driver->query("SELECT * FROM " . $this->name);
            while ($row = $result->fetch_assoc()) {
                $this->assertEquals($this->accountName, $row["NAME"]);
                $count += 1;
            }
            $this->assertEquals(1, $count);
            return;
        }
        $this->assertTrue(false);
    }

    public function testIfAccountAlreadyExistsShouldReturnTrue()
    {
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currenciesTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(true));
        $this->table->addAccount($this->account);
        $isAlreadyInDB = $this->table->doesAccountExists($this->accountName, $this->userID);
        $this->assertTrue($isAlreadyInDB);
    }

    public function testIfAccountAlreadyExistsShouldReturnFalse()
    {
        $isAlreadyInDB = $this->table->doesAccountExists($this->accountName, $this->userID);
        $this->assertFalse($isAlreadyInDB);
    }

    public function testGetAccountsFromUserID()
    {
        $this->usersTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->userID)->will($this->returnValue(true));
        $this->currenciesTable->expects($this->once())
            ->method('checkIfIDExists')->with($this->currencyID)->will($this->returnValue(true));
        $this->currenciesTable->expects($this->once())
            ->method('getCurrencyFromID')->with($this->currencyID)->will($this->returnValue(["NAME" => $this->account->getCurrency()]));
        $this->usersTable->expects($this->once())
            ->method('getUserFromID')->with($this->userID)->will($this->returnValue(["NAME" => $this->account->getUser()]));
        $this->table->addAccount($this->account);
        $accounts = $this->table->getAccountsFromUserID($this->userID);
        $this->assertEquals(1, count($accounts));
        $this->assertEquals($this->account, $accounts[0]);
    }
}
