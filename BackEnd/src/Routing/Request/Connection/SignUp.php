<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/16/2019
 * Time: 7:03 PM
 */

namespace BackEnd\Routing\Request\Connection;
use BackEnd\Routing\Request\PostRequest;

class SignUp extends PostRequest
{
    protected $email = "";
    protected $password = "";
    protected $firstName = "";
    protected $lastName = "";
    protected $registeredDate = "";
    protected $lastConnection = "";
    protected $validationID = "";
    private $usersTable;

    public function __construct($usersTable)
    {
        $this->usersTable = $usersTable;
    }

    public function init(){
        parent::init();
        $registeredDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->registeredDate = $registeredDate->format("Y-m-d H:i:s");
        $this->lastConnection = $this->registeredDate;
        $this->validationID = $registeredDate->getTimestamp();
        $missingParameters = array();

        if($this->email == ""){
            $missingParameters[] = "email";
        }
        if($this->password == ""){
            $missingParameters[] = "password";
        }
        if($this->firstName == ""){
            $missingParameters[] = "first_name";
        }
        if($this->lastName == ""){
            $missingParameters[] = "last_name";
        }
        if(sizeof($missingParameters) > 0){
            throw new MissingParametersException($missingParameters, "SignIn");
        }
    }

    public function getEmail(){
        return $this->email;
    }

    public function getFirstName(){
        return $this->firstName;
    }
    public function getLastName(){
        return $this->lastName;
    }

    public function getRegisteredDate(){
        return $this->registeredDate;
    }

    public function getLastConnection(){
        return $this->lastConnection;
    }

    public function getValidationID(){
        return $this->validationID;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    public function getResponse()
    {
        return new \BackEnd\Routing\Response\Connection\SignUp($this, $this->usersTable);
    }
}