<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 6:45 PM
 */

namespace BackEnd\Routing\Request\Currency;
use BackEnd\Database\DBTables;
use BackEnd\Database\Database;
use BackEnd\User;

class CurrencyRequestFactory
{
    /** @var Database */
    private $database;
    public function __construct($database)
    {
        $this->database = $database;
    }

    public function createRequest($type){
        $postArray = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        switch($type){
            case "Create":
                return new CurrencyCreation($this->database->getTableByName(DBTables::CURRENCIES),
                    $this->database->getTableByName(DBTables::USERS),
                    new User(),
                    $postArray);
            case "Delete":
                return new DeleteCurrency($this->database->getTableByName(DBTables::CURRENCIES),
                    $this->database->getTableByName(DBTables::USERS),
                    new User(),
                    $postArray);
            case "RetrieveAll":
                return new RetrieveAllCurrencies($this->database->getTableByName(DBTables::CURRENCIES), $postArray);
            default:
                throw new \InvalidArgumentException("Request type: ".$type." not found.");
        }
    }

    public function getDatabase(){
        return $this->database;
    }
}