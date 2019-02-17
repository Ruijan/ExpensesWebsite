<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/16/2019
 * Time: 7:41 PM
 */

namespace BackEnd\Routing\Response\Connection;


class SignUp
{
    private $request;
    private $usersTable;
    private $response = '';
    public function __construct(\BackEnd\Routing\Request\Connection\SignUp $request, $usersTable)
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
        $user = $this->createUserFromRequest();
        $alreadyExist = $this->usersTable->checkIfEmailExists($this->request->getEmail());
        if(!$alreadyExist){
            $this->usersTable->addUser($user);
            $this->response = 'Added';
        }
    }

    public function getAnswer(){
        return $this->response;
    }

    /**
     * @return array
     */
    protected function createUserFromRequest(): array
    {
        $user = ["EMAIL" => $this->request->getEmail(),
            "PASSWORD" => $this->request->getPassword(),
            "FIRST_NAME" => $this->request->getFirstName(),
            "LAST_NAME" => $this->request->getLastName(),
            "REGISTERED_DATE" => $this->request->getRegisteredDate(),
            "LAST_CONNECTION" => $this->request->getLastConnection(),
            "VALIDATION_ID" => $this->request->getValidationID()];
        return $user;
    }
}