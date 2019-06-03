<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/3/2019
 * Time: 8:07 PM
 */

namespace BackEnd\Routing\Response\Connection;

use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\User;

class SignIn
{
    private $request;
    private $usersTable;
    private $response = '';
    private $user;
    public function __construct(\BackEnd\Routing\Request\Connection\SignIn $request, DBUsers $usersTable, $user)
    {
        $this->request = $request;
        $this->usersTable = $usersTable;
        $this->user = $user;
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
        $this->user->connect($this->usersTable, $email, $password);
        $this->response = json_encode(array(
            "STATUS" => "ERROR",
            "ERROR_MESSAGE" => 'Email or password invalid'));

        if($this->user->isConnected()){
            echo "here is the user";
            print_r($this->user->asDict());
            $this->response = json_encode($this->createResponseFromUser($this->user->asDict()));
        }
    }

    private function createResponseFromUser($user){
        $response = array(
            "STATUS" => "OK",
            "DATA" => array( "FIRST_NAME" => $user["FIRST_NAME"],
                "LAST_NAME" => $user["NAME"],
                "USER_ID" => $user["ID"],
                "EMAIL_VALIDATED" => $user["EMAIL_VALIDATED"],
                "EMAIL" => $user["EMAIL"],
                "SESSION_ID" => $user["SESSION_ID"])
        );
        return $response;
    }

    public function getAnswer(){
        return $this->response;
    }
}