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
use BackEnd\Routing\Request\RequestFactory;
use BackEnd\User;

class ConnectionRequestFactory extends RequestFactory
{
    public function createRequest($type, $data){
        switch($type){
            case "SignIn":
                return new SignIn($this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "Delete":
                return new DeleteUser($this->database->getTableByName(DBTables::USERS), $data);
            case "SignUp":
                return new SignUp($this->database->getTableByName(DBTables::USERS), $data);
            default:
                throw new \InvalidArgumentException("Request type: ".$type." not found.");
        }
    }

    public function getDatabase(){
        return $this->database;
    }
}