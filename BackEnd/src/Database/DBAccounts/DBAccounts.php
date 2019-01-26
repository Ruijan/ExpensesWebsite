<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/12/2019
 * Time: 4:46 PM
 */

namespace BackEnd\Database\DBAccounts;
use BackEnd\Database\DBTable;
use BackEnd\Database\DBAccounts\AccountDuplicationException;
use BackEnd\Database\DBAccounts\UserIDException;
use BackEnd\Database\DBAccounts\CurrencyIDException;


class DBAccounts extends DBTable
{
    private $usersTable;
    private $currenciesTable;

    public function __construct($database, $usersTable, $currenciesTable)
    {
        parent::__construct($database, "accounts");
        $this->usersTable = $usersTable;
        $this->currenciesTable = $currenciesTable;
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL,
                        USER_ID int(11) NOT NULL,
                        CURRENCY_ID int(11) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        CURRENT_AMOUNT int(11) DEFAULT 0,
                        PRIMARY KEY (ID)";
    }

    public function addAccount($name, $currentAmount, $userID, $currencyID)
    {
        $this->checkParameters($name, $currentAmount, $userID, $currencyID);
        $currentUTCDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $query = 'INSERT INTO ' . $this->name . ' (NAME, USER_ID, CURRENCY_ID, ADDED_DATE, CURRENT_AMOUNT) VALUES ("' .
            $this->driver->real_escape_string($name) . '", "' . $this->driver->real_escape_string($userID) . '", "' . $this->driver->real_escape_string($currencyID) . '", "' .
            $currentUTCDate->format("Y-m-d H:i:s") . '", "' . $this->driver->real_escape_string($currentAmount) . '")';
        if ($this->driver->query($query) === FALSE) {
            throw new DBAccount\InsertionException($name, $currentAmount, $userID, $this->name, $this->driver->error_list[0]["error"]);
        }
    }

    protected function checkParameters($name, $currentAmount, $userID, $currencyID): void
    {
        if ($this->usersTable->checkIfIDExists($userID) == false) {
            throw new UserIDException($name, $currentAmount, $userID, $this->name);
        }
        if ($this->currenciesTable->checkIfIDExists($currencyID) == false) {
            throw new CurrencyIDException($name, $currentAmount, $userID, $this->name);
        }
        if ($this->doesAccountExists($name, $userID) !== false) {
            throw new AccountDuplicationException($name, $currentAmount, $userID, $this->name);
        }
    }

    public function doesAccountExists($accountName, $userID)
    {
        $result = $this->driver->query("SELECT ID FROM " . $this->name . " WHERE NAME='" . $this->driver->real_escape_string($accountName) .
            "' AND USER_ID='" . $this->driver->real_escape_string($userID) . "'");
        while ($result and $row = $result->fetch_assoc()) {
            return true;
        }
        return false;
    }

    public function getUsersTable()
    {
        return $this->usersTable;
    }

    public function getCurrenciesTable()
    {
        return $this->currenciesTable;
    }

    public function getAccountsFromUserID($userID)
    {
        $accounts = array();
        $result = $this->driver->query("SELECT ID, NAME, USER_ID, CURRENT_AMOUNT, ADDED_DATE FROM " . $this->name . " WHERE USER_ID='" . $this->driver->real_escape_string($userID) . "'");
        while ($result and $row = $result->fetch_assoc()) {
            $accounts[] = $row;
        }
        return $accounts;
    }
}