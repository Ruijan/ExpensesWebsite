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
use BackEnd\Database\DBCurrency;
use BackEnd\Database\DBExpenses;
use BackEnd\Database\DBPayee;
use BackEnd\Database\DBUser;
use BackEnd\Database\DBAccount\DBAccount;

class DBTableFactory
{
    static public function createTable($tableName, $database){

        switch ($tableName) {
            case "DBCategories":
                return new DBCategories($database, $database->getTableByName("dbuser"));
            case "DBSubCategories":
                return new DBSubCategories($database, $database->getTableByName("dbuser"), $database->getTableByName("dbcategories"));
            case "DBCurrency":
                return new DBCurrency($database);
            case "DBExpenses":
                return new DBExpenses($database);
            case "DBPayee":
                return new DBPayee($database);
            case "DBUser":
                return new DBUser($database);
            case "DBAccount":
                return new DBAccount($database, $database->getTableByName("dbuser"), $database->getTableByName("dbcurrencies"));
        }
        throw new \Exception("Invalid table type: ". $tableName);
    }
}