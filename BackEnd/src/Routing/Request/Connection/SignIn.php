<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/7/2019
 * Time: 11:12 PM
 */
namespace BackEnd\Routing\Request\Connection;

use BackEnd\Routing\Request\PostRequest;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\User;

class SignIn extends PostRequest
{
    protected $email = "";
    protected $password = "";
    private $usersTable;
    public function __construct($usersTable)
    {
        $this->usersTable = $usersTable;
    }

    public function init(){
        parent::init();
        $missingParameters = array();

        if($this->email == ""){
            $missingParameters[] = "email";
        }
        if($this->password == ""){
            $missingParameters[] = "password";
        }
        if(count($missingParameters) > 0){
            throw new MissingParametersException($missingParameters, "SignIn");
        }
    }

    public function getResponse()
    {
        return new \BackEnd\Routing\Response\Connection\SignIn($this, $this->usersTable, new User());
    }

    public function getEmail(){
        return $this->email;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }
}