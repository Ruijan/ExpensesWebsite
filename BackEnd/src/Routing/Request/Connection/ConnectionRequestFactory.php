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
use BackEnd\User;

class ConnectionRequestFactory
{
    private $database;
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function createRequest($type){
        $postArray = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        switch($type){
            case "SignIn":
                return new SignIn($this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            case "SignUp":
                return new SignUp($this->database->getTableByName(DBTables::USERS), $postArray);
            default:
                throw new \InvalidArgumentException("Request type: ".$type." not found.");
        }
    }

    public function getDatabase(){
        return $this->database;
    }
}