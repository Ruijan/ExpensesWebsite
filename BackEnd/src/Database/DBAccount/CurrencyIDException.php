<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 11:38 AM
 */

namespace BackEnd\Database\DBAccount;
use BackEnd\Database\DBAccount\InsertionException;

class CurrencyIDException extends InsertionException
{
    public function __construct($name, $currentAmount, $userID, $tableName)
    {
        parent::__construct($name, $currentAmount, $userID, $tableName, "Currency ID does not exist.");
    }
}