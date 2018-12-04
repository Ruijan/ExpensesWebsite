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


class DBTableFactory
{
    static public function createTable($tableName, $database){

        switch ($tableName) {
            case "DBCategories":
                return new DBCategories($database);
            case "DBSubCategories":
                return new DBSubCategories($database);
            case "DBCurrency":
                return new DBCurrency($database);
            case "DBExpenses":
                return new DBExpenses($database);
            case "DBPayee":
                return new DBPayee($database);
            case "DBPayer":
                return new DBPayer($database);
        }
        throw new \Exception("Invalid table type: ". $tableName);
    }
}