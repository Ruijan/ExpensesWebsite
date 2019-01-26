<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 6:11 PM
 */

namespace BackEnd\Database\DBExpenses;

class InsertionKeyException extends \Exception{
    public function __construct($key){
        parent::__construct($key." should not be empty.");
    }
}