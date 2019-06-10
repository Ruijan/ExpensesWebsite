<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/16/2019
 * Time: 7:03 PM
 */

namespace BackEnd\Routing\Request\Connection;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Database\DBUsers\InsertionException;
use BackEnd\Database\DBUsers\UndefinedUserEmail;
use BackEnd\Routing\Request\Request;
use BackEnd\Routing\Request\MissingParametersException;

class SignUp extends Request
{
    protected $email = "";
    protected $password = "";
    protected $firstName = "";
    protected $lastName = "";
    protected $registeredDate = "";
    protected $lastConnection = "";
    protected $validationID = "";
    /** @var DBUsers */
    private $usersTable;

    public function __construct($usersTable, $data)
    {
        $mandatoryFields = ["email", "password", "first_name", "last_name"];
        parent::__construct($data, $mandatoryFields);
        $this->usersTable = $usersTable;
        $registeredDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->registeredDate = $registeredDate->format("Y-m-d H:i:s");
        $this->lastConnection = $this->registeredDate;
        $this->validationID = $registeredDate->getTimestamp();
    }

    public function execute()
    {
        try{
            $this->checkRequiredParameters();
            $this->tryAddingUser();
            $addedUser = $this->usersTable->getUserFromEmail($this->email);
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = $addedUser;
        }catch(InsertionException | MissingParametersException | UndefinedUserEmail $exception){
            $this->response["STATUS"] = "ERROR";
            $this->response["ERROR_MESSAGE"] = $exception->getMessage();
        }
        $this->response = json_encode($this->response);
    }


    public function getUsersTable(){
        return $this->usersTable;
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

    /**
     * @throws InsertionException
     */
    protected function tryAddingUser(): void
    {
        $user = ["FIRST_NAME" => $this->firstName,
            "LAST_NAME" => $this->lastName,
            "EMAIL" => $this->email,
            "PASSWORD" => $this->password,
            "REGISTERED_DATE" => $this->registeredDate,
            "LAST_CONNECTION" => $this->lastConnection,
            "VALIDATION_ID" => $this->validationID];
        $this->usersTable->addUser($user);
    }

}