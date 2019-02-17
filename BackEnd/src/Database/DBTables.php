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
    public const USERS = "dbusers";
    public const ACCOUNTS = "dbaccounts";
    const CURRENCIES = "dbcurrencies";
    const EXPENSES = "dbexpenses";
    const CATEGORIES = "dbcategories";
    const SUBCATEGORIES = "dbsubcategories";
    const EXPENSES_STATES = "dbexpensestates";
    const PAYEES = "dbpayees";
}