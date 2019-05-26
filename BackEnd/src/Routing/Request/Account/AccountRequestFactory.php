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

class AccountRequestFactory
{
    private $database;
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function createRequest($type){

        switch($type){
            case "AccountCreation":
                return new AccountCreation($this->database->getTableByName(DBTables::USERS),
                    $this->database->getTableByName(DBTables::ACCOUNTS));
            default:
                throw new \InvalidArgumentException("Request type: ".$type." not found.");
        }
    }

    public function getDatabase(){
        return $this->database;
    }
}