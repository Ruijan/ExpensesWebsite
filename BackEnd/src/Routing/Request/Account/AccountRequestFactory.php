<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/26/2019
 * Time: 10:16 PM
 */

namespace BackEnd\Routing\Request\Account;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\User;

class AccountRequestFactory
{
    private $database;
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function createRequest($type){
        $postArray = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        switch($type){
            case "Create":
                return new AccountCreation($this->database->getTableByName(DBTables::ACCOUNTS),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            case "Delete":
                return new DeleteAccount($this->database->getTableByName(DBTables::ACCOUNTS),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            case "Retrieve":
                return new RetrieveAccounts($this->database->getTableByName(DBTables::ACCOUNTS),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            default:
                throw new \InvalidArgumentException("Request type: ".$type." not found.");
        }
    }

    public function getDatabase(){
        return $this->database;
    }
}