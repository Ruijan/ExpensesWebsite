<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 11:04 PM
 */

namespace BackEnd\Database;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Database\DBUsers\DBUsers;
use mysql_xdevapi\Exception;
use BackEnd\Database\DBCurrencies;
use BackEnd\Database\DBExpenses\DBExpenses;
use BackEnd\Database\DBPayees;
use BackEnd\Database\DBAccounts\DBAccounts;
use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Database\DBTables;

class DBTableFactory
{
    static public function createTable($tableName, $database){

        switch ($tableName) {
            case DBTables::CATEGORIES:
                return new DBCategories($database, $database->getTableByName(DBTables::USERS));
            case DBTables::SUBCATEGORIES:
                return new DBSubCategories($database, $database->getTableByName(DBTables::USERS), $database->getTableByName(DBTables::CATEGORIES));
            case DBTables::CURRENCIES:
                return new DBCurrencies($database);
            case DBTables::EXPENSES:
                return new DBExpenses($database);
            case DBTables::PAYEES:
                return new DBPayees($database);
            case DBTables::USERS:
                return new DBUsers($database);
            case DBTables::ACCOUNTS:
                return new DBAccounts($database, $database->getTableByName(DBTables::USERS), $database->getTableByName(DBTables::CURRENCIES));
            case DBTables::EXPENSES_STATES:
                return new DBExpenseStates($database);
        }
        throw new \Exception("Invalid table type: ". $tableName);
    }
}