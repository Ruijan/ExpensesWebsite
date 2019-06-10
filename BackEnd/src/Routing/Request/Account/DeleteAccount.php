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


class DeleteAccount extends Request
{
    protected $name;
    protected $sessionId;
    protected $userId;

    /** @var \BackEnd\User */
    protected $user;
    /** @var DBAccounts */
    protected $accountsTable;
    /** @var DBUsers */
    protected $usersTable;

    public function __construct($accountsTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name", "session_id", "user_id"];
        parent::__construct($data, $mandatoryFields);
        $this->accountsTable = $accountsTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
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
            $this->response["STATUS"] = "ERROR";
            $this->response["ERROR_MESSAGE"] = $exception->getMessage();
        }
        $this->response = json_encode($this->response);
    }

    public function getAccountsTable(){
        return $this->accountsTable;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    /**
     * @throws InvalidSessionException
     */
    protected function tryConnectingUser(): void
    {
        $this->user->connectWithSessionID($this->usersTable, $this->sessionId, $this->userId);
        if (!$this->user->isConnected()) {
            throw new InvalidSessionException("DeleteAccount");
        }
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