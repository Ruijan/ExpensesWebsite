<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 6:40 PM
 */

namespace BackEnd\Database\DBExpenseStates;

class InsertionException extends \Exception
{
    public function __construct($name, $tableName, $reason)
    {
        parent::__construct("Could not add expense state " . $name . " in table " . $tableName . ". Reason: " . $reason);
    }
}