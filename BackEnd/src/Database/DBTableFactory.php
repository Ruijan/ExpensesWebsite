<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 11:04 PM
 */

namespace BackEnd\Database;
use mysql_xdevapi\Exception;

use BackEnd\Database\DBCategories;
use BackEnd\Database\DBSubCategories;
use BackEnd\Database\DBCurrencies;
use BackEnd\Database\DBExpenses;
use BackEnd\Database\DBPayees;
use BackEnd\Database\DBUsers;
use BackEnd\Database\DBAccounts\DBAccounts;

class DBTableFactory
{
    static public function createTable($tableName, $database){

        switch ($tableName) {
            case "DBCategories":
                return new DBCategories($database, $database->getTableByName("dbuser"));
            case "DBSubCategories":
                return new DBSubCategories($database, $database->getTableByName("dbuser"), $database->getTableByName("dbcategories"));
            case "DBCurrencies":
                return new DBCurrencies($database);
            case "DBExpenses":
                return new DBExpenses($database);
            case "DBPayees":
                return new DBPayees($database);
            case "DBUsers":
                return new DBUsers($database);
            case "DBAccounts":
                return new DBAccounts($database, $database->getTableByName("dbuser"), $database->getTableByName("dbcurrencies"));
        }
        throw new \Exception("Invalid table type: ". $tableName);
    }
}