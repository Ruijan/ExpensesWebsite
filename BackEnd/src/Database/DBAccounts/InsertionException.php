<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 11:36 AM
 */

namespace BackEnd\Database\DBAccounts;

class InsertionException extends \Exception
{
    public function __construct($account, $tableName, $reason)
    {
        parent::__construct("Couldn't insert account " . implode(" ,", $account->asDict()) .
            ", in " . $tableName . ". Reason: " . $reason);
    }
}

