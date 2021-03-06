<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 11:41 AM
 */

namespace BackEnd\Database\DBAccounts;
use BackEnd\Database\DBAccounts\InsertionException;

class AccountDuplicationException extends InsertionException
{
    public function __construct($account, $tableName)
    {
        parent::__construct($account, $tableName, "This account name already exists for this payer.");
    }
}