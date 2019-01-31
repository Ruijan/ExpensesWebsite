<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 11:40 AM
 */

namespace BackEnd\Database\DBAccounts;
use BackEnd\Database\DBAccounts\InsertionException;

class UserIDException extends InsertionException
{
    public function __construct($account, $tableName)
    {
        parent::__construct($account, $tableName, "User ID does not exist.");
    }
}