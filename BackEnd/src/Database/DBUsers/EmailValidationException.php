<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/31/2019
 * Time: 10:03 PM
 */

namespace BackEnd\Database\DBUsers;


use Throwable;

class EmailValidationException extends \Exception
{
    public function __construct(string $validationID, string $tableName, string $reason)
    {
        $message = "Couldn't validate email address with id " . $validationID .
            " in " . $tableName .
            ". Reason: " . $reason;
        parent::__construct($message);
    }
}