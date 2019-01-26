<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 6:12 PM
 */
namespace BackEnd\Database\DBExpenses;

class WrongTypeKeyException extends \Exception{
    public function __construct($key, $value, $type){
        parent::__construct($key . " with value ".$value." has an invalid type: ".gettype($value)." instead of ".$type.".");
    }
}