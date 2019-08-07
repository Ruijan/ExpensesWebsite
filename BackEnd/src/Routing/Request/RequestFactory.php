<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/24/2019
 * Time: 8:19 PM
 */

namespace BackEnd\Routing\Request;


use BackEnd\Database\Database;

abstract class RequestFactory
{
    /** @var Database */
    protected $database;
    public function __construct($database)
    {
        $this->database = $database;
    }

    abstract public function createRequest(string $type, $data);
    public function getDatabase(){
        return $this->database;
    }
}