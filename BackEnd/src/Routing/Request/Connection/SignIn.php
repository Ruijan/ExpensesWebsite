<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/7/2019
 * Time: 11:12 PM
 */
namespace BackEnd\Routing\Request\Connection;

use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Request;
use BackEnd\User;

class SignIn extends Request
{
    protected $email;
    protected $password;
    /** @var User */
    protected $user;
    /** @var DBUsers  */
    private $usersTable;

    /**
     * SignIn constructor.
     * @param $usersTable
     * @param $user
     * @param $data
     */
    public function __construct($usersTable, $user, $data)
    {
        $mandatoryFields = ["email", "password"];
        parent::__construct($data, $mandatoryFields, "SignIn");
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute()
    {
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array( "FIRST_NAME" => $this->user->getFirstName(),
                "LAST_NAME" => $this->user->getLastName(),
                "USER_ID" => $this->user->getID(),
                "EMAIL_VALIDATED" => $this->user->getEmail(),
                "EMAIL" => $this->user->getEmail(),
                "SESSION_ID" => $this->user->getSessionID());
        }catch(InvalidCredentialsException | MissingParametersException $exception){
            $this->response["STATUS"] = "ERROR";
            $this->response["ERROR_MESSAGE"] = $exception->getMessage();
        }
        $this->response = json_encode($this->response);
    }

    /**
     * @return DBUsers
     */
    public function getUsersTable(){
        return $this->usersTable;
    }

    public function getUser(){
        return $this->user;
    }

    protected function tryConnectingUser(): void
    {
        $this->user->connect($this->usersTable, $this->email, $this->password);
        if (!$this->user->isConnected()) {
            throw new InvalidCredentialsException("SignIn");
        }
    }
}