<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/31/2019
 * Time: 10:13 PM
 */

namespace BackEnd\Database\DBSubCategories;


use Throwable;

class InsertionException extends \Exception
{
    public function __construct($subCategory, $tableName, $reason, int $code = 0, Throwable $previous = null)
    {
        $message = "Couldn't insert sub category ".implode(" ,", $subCategory)." in ".$tableName.
            ". Reason: " . $reason;
        parent::__construct($message, $code, $previous);
    }
}