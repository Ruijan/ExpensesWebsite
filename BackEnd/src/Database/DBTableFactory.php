<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 11:04 PM
 */

namespace src;
use mysql_xdevapi\Exception;

require_once ("DBCategories.php");
require_once ("DBCurrency.php");
require_once ("DBSubCategories.php");
require_once ("DBExpenses.php");
require_once ("DBPayee.php");
require_once ("DBPayer.php");
require_once ("DBAccount.php");


class DBTableFactory
{
    static public function createTable($tableName, $database){

        switch ($tableName) {
            case "DBCategories":
                return new DBCategories($database, $database->getTableByName("dbpayer"));
            case "DBSubCategories":
                return new DBSubCategories($database, $database->getTableByName("dbpayer"), $database->getTableByName("dbcategories"));
            case "DBCurrency":
                return new DBCurrency($database);
            case "DBExpenses":
                return new DBExpenses($database);
            case "DBPayee":
                return new DBPayee($database);
            case "DBPayer":
                return new DBPayer($database);
            case "DBAccount":
                return new DBAccount($database, $database->getTableByName("dbpayer"));
        }
        throw new \Exception("Invalid table type: ". $tableName);
    }
}