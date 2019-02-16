<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/16/2019
 * Time: 4:33 PM
 */

namespace BackEnd\Database;


abstract class DBTables
{
    public const Users = "dbusers";
    public const Accounts = "dbaccounts";
    const Currencies = "dbcurrencies";
    const Expenses = "dbexpenses";
    const Categories = "dbcategories";
    const SubCategories = "dbsubcategories";
    const ExpenseStates = "dbexpensestates";
    const Payees = "dbpayees";
}