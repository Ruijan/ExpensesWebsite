<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/3/2019
 * Time: 8:07 PM
 */

namespace BackEnd\Routing\Response\Connection;

use BackEnd\Database\DBUsers\DBUsers;

class SignIn
{
    private $request;
    private $usersTable;
    private $response = '';
    public function __construct(\BackEnd\Routing\Request\Connection\SignIn $request, DBUsers $usersTable)
    {
        $this->request = $request;
        $this->usersTable = $usersTable;
    }

    public function getRequest(){
        return $this->request;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    public function execute(){
        $email = $this->request->getEmail();
        $password = $this->request->getPassword();
        $credentialsValid = $this->usersTable->areCredentialsValid($email, $password);
        $this->response = json_encode(array(
            "Status" => "ERROR",
            "ERROR_MESSAGE" => 'Email or password invalid'));
        if($credentialsValid){
            $user = $this->usersTable->getUserFromEmail($email);
            $this->response = json_encode($this->createResponseFromUser($user));
        }
    }

    private function createResponseFromUser($user){
        $response = array(
            "Status" => "OK",
            "Data" => array( "first_name" => $user["FIRST_NAME"],
                "last_name" => $user["NAME"],
                "user_ID" => $user["ID"],
                "email_validated" => $user["EMAIL_VALIDATED"],
                "email" => $user["EMAIL"])
        );
        return $response;
    }

    public function getAnswer(){
        return $this->response;
    }
}