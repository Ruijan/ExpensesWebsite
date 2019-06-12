<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/12/2019
 * Time: 10:27 PM
 */

namespace BackEnd\Routing\Request\Account;

use BackEnd\Database\DBAccounts\DBAccounts;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;

class RetrieveAccounts extends Request
{
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
        $mandatoryFields = ["session_id", "user_id"];
        parent::__construct($data, $mandatoryFields);
        $this->accountsTable = $accountsTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(){
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $accounts = $this->accountsTable->getAccountsFromUserID($this->userId);
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array();
            foreach($accounts as $account){
                $this->response["DATA"][] = $account->asDict();
            }
        }
        catch(MissingParametersException | InvalidSessionException $exception){
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
}