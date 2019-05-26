<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 9:26 PM
 */

namespace BackEnd\Routing\Request\Connection;

use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\PostRequest;
use BackEnd\Routing\Request\Connection\SignIn;
use BackEnd\Routing\Request\Connection\SignUp;
use http\Exception\InvalidArgumentException;

class ConnectionRequestFactory
{
    private $database;
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function createRequest($type){

        switch($type){
            case "SignIn":
                return new SignIn($this->database->getTableByName(DBTables::USERS));
            case "SignUp":
                return new SignUp($this->database->getTableByName(DBTables::USERS));
            default:
                throw new \InvalidArgumentException("Request type: ".$type." not found.");
        }
    }

    public function getDatabase(){
        return $this->database;
    }
}