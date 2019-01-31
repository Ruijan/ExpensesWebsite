<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/31/2019
 * Time: 9:54 PM
 */

namespace BackEnd\Database\DBUsers;


use Throwable;

class InsertionException extends \Exception
{
    public function __construct($user, string $tableName, string $reason,  int $code = 0, Throwable $previous = null)
    {
        $message = "Couldn't insert user " . implode(" ,", $user) .
            " in " . $tableName.
            ". Reason: " . $reason;
        parent::__construct($message, $code, $previous);
    }
}