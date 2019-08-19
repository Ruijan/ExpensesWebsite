<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 11:38 AM
 */

namespace BackEnd\Routing\Request\Account;
use BackEnd\Database\DBAccounts\{DBAccounts, UndefinedAccountException};
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;


class DeleteAccount extends AccountRequest
{
    protected $name;

    public function __construct($accountsTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name"];
        parent::__construct("DeleteAccounts",
            $mandatoryFields,
            $accountsTable,
            $usersTable,
            $user,
            $data);
    }

    public function execute(){
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $this->checkIfAccountExists();
            $this->accountsTable->deleteAccountFromNameAndUser($this->name,
                $this->userId);
            $this->response["STATUS"] = "OK";
        }
        catch(MissingParametersException | InvalidSessionException |
        UndefinedAccountException $exception){
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    /**
     * @throws UndefinedAccountException
     */
    protected function checkIfAccountExists(): void
    {
        $accountExists = $this->accountsTable->doesAccountExists($this->name, $this->userId);
        if (!$accountExists) {
            throw new UndefinedAccountException($this->name, $this->userId);
        }
    }
}