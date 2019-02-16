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
            case DBTables::Categories:
                return new DBCategories($database, $database->getTableByName(DBTables::Users));
            case DBTables::SubCategories:
                return new DBSubCategories($database, $database->getTableByName(DBTables::Users), $database->getTableByName(DBTables::Categories));
            case DBTables::Currencies:
                return new DBCurrencies($database);
            case DBTables::Expenses:
                return new DBExpenses($database);
            case DBTables::Payees:
                return new DBPayees($database);
            case DBTables::Users:
                return new DBUsers($database);
            case DBTables::Accounts:
                return new DBAccounts($database, $database->getTableByName(DBTables::Users), $database->getTableByName(DBTables::Currencies));
            case DBTables::ExpenseStates:
                return new DBExpenseStates($database);
        }
        throw new \Exception("Invalid table type: ". $tableName);
    }
}