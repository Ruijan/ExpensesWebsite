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
use BackEnd\Routing\Request\RequestFactory;
use BackEnd\User;

class AccountRequestFactory extends RequestFactory
{

    public function createRequest($type, $data){
        switch($type){
            case "Create":
                return new AccountCreation($this->database->getTableByName(DBTables::ACCOUNTS),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "Delete":
                return new DeleteAccount($this->database->getTableByName(DBTables::ACCOUNTS),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "Retrieve":
                return new RetrieveAccounts($this->database->getTableByName(DBTables::ACCOUNTS),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            default:
                throw new \InvalidArgumentException("Request type: ".$type." not found.");
        }
    }
}