<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/31/2019
 * Time: 9:31 PM
 */
namespace BackEnd\Database\DBCategories;

class InsertionException extends \Exception
{
    public function __construct($category, $tableName, $reason)
    {
        parent::__construct("Could not add category " . implode(" ,", $category) .
            " in table " . $tableName .
            ". Reason: " . $reason);
    }
}