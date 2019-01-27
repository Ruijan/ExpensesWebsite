<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 6:10 PM
 */

namespace BackEnd\Database\DBExpenses;

class InsertionException extends \Exception
{
    public function __construct($message)
    {
        parent::__construct("Could not add expense in table expenses: " . $message);
    }
}