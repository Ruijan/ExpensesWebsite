<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/31/2019
 * Time: 9:54 PM
 */

namespace BackEnd\Database;


use Throwable;

class InsertionException extends \Exception
{
    public function __construct(string $elementName, $element, string $tableName, string $reason,  int $code = 0, Throwable $previous = null)
    {
        $message = "Couldn't insert " . $elementName . " " . implode(" ,", $element) .
            " in " . $tableName.
            ". Reason: " . $reason;
        parent::__construct($message, $code, $previous);
    }
}