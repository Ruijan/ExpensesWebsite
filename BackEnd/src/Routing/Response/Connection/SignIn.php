<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/3/2019
 * Time: 8:07 PM
 */

namespace BackEnd\Routing\Response\Connection;

class SignIn
{
    private $request;
    private $usersTable;
    private $response = '';
    public function __construct(\BackEnd\Routing\Request\Connection\SignIn $request, $usersTable)
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
        $this->response = 'ERROR: Email or password invalid';
        if($credentialsValid){
            $this->response = 'Connected';
        }
    }

    public function getAnswer(){
        return $this->response;
    }
}