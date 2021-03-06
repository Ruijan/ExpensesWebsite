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
use BackEnd\Routing\Request\RequestFactory;
use BackEnd\User;

class CurrencyRequestFactory extends RequestFactory
{
    public function createRequest($type, $data){
        switch($type){
            case "Create":
                return new CurrencyCreation($this->database->getTableByName(DBTables::CURRENCIES),
                    $this->database->getTableByName(DBTables::USERS),
                    new User(),
                    $data);
            case "Delete":
                return new DeleteCurrency($this->database->getTableByName(DBTables::CURRENCIES),
                    $this->database->getTableByName(DBTables::USERS),
                    new User(),
                    $data);
            case "RetrieveAll":
                return new RetrieveAllCurrencies($this->database->getTableByName(DBTables::CURRENCIES), $data);
            default:
                throw new \InvalidArgumentException("Request type: ".$type." not found.");
        }
    }
}