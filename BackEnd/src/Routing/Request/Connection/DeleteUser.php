<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/8/2019
 * Time: 5:39 PM
 */

namespace BackEnd\Routing\Request\Connection;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Database\DBUsers\UndefinedUserEmail;
use BackEnd\Routing\Request\Request;
use BackEnd\Routing\Request\MissingParametersException;

class DeleteUser extends Request
{
    protected $email;
    protected $password;
    /** @var DBUsers  */
    private $usersTable;

    /**
     * DeleteUser constructor.
     * @param DBUsers $usersTable
     * @param array $data
     */
    public function __construct($usersTable, array $data)
    {
        $mandatoryFields = ["email", "password"];
        parent::__construct($data, $mandatoryFields, "DeleteUser");
        $this->usersTable = $usersTable;
    }

    public function execute()
    {
        try{
            $this->checkRequiredParameters();
            $this->checkCredentials();
            $this->usersTable->deleteUserFromEmail($this->email);
            $this->response["STATUS"] = "OK";
        }
        catch(MissingParametersException | InvalidCredentialsException | UndefinedUserEmail $exception){
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    /**
     * @return \BackEnd\Database\DBUsers\DBUsers
     */
    public function getUsersTable(){
        return $this->usersTable;
    }

    protected function checkCredentials(): void
    {
        if (!$this->usersTable->areCredentialsValid($this->email, $this->password)) {
            throw new InvalidCredentialsException("DeleteUser");
        }
    }
}