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
use BackEnd\Account\Account;


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

    public function addAccount($account)
    {
        $this->checkParameters($account);
        $currentUTCDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $query = 'INSERT INTO ' . $this->name . ' (NAME, USER_ID, CURRENCY_ID, ADDED_DATE, CURRENT_AMOUNT) VALUES ("' .
            $this->driver->real_escape_string($account->getName()) . '", "' . $this->driver->real_escape_string($account->getUserID()) .
            '", "' . $this->driver->real_escape_string($account->getCurrencyID()) . '", "' .
            $currentUTCDate->format("Y-m-d H:i:s") . '", "' . $this->driver->real_escape_string($account->getCurrentAmount()) . '")';
        if ($this->driver->query($query) === FALSE) {
            throw new DBAccount\InsertionException($account, $this->name, $this->driver->error_list[0]["error"]);
        }
        $account->setID($this->driver->insert_id);
    }

    protected function checkParameters($account): void
    {
        if ($this->usersTable->checkIfIDExists($account->getUserID()) == false) {
            throw new UserIDException($account, $this->name);
        }
        if ($this->currenciesTable->checkIfIDExists($account->getCurrencyID()) == false) {
            throw new CurrencyIDException($account, $this->name);
        }
        if ($this->doesAccountExists($account->getName(), $account->getUserID()) !== false) {
            throw new AccountDuplicationException($account, $this->name);
        }
    }

    public function doesAccountExists($accountName, $userID)
    {
        $result = $this->driver->query("SELECT ID FROM " . $this->name . " WHERE NAME='" . $this->driver->real_escape_string($accountName) .
            "' AND USER_ID='" . $this->driver->real_escape_string($userID) . "'");
        while ($result and $result->fetch_assoc()) {
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
        $result = $this->driver->query("SELECT ID, NAME, USER_ID, CURRENT_AMOUNT, CURRENCY_ID, ADDED_DATE FROM " . $this->name . " WHERE USER_ID='" . $this->driver->real_escape_string($userID) . "'");
        while ($result and $row = $result->fetch_assoc()) {
            $user = $this->usersTable->getUserFromID($row["USER_ID"]);
            $currency = $this->currenciesTable->getCurrencyFromID($row["CURRENCY_ID"]);
            $row["USER"] = $user["NAME"];
            $row["CURRENCY"] = $currency["NAME"];
            $accounts[] = new Account($row);

        }
        return $accounts;
    }
}