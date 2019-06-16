<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 9:00 PM
 */

namespace BackEnd\Routing\Request\Account;


use BackEnd\Database\DBAccounts\DBAccounts;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;

abstract class AccountRequest extends Request
{
    protected $sessionId;
    protected $userId;
    /** @var \BackEnd\User */
    protected $user;
    /** @var DBAccounts */
    protected $accountsTable;
    /** @var DBUsers */
    protected $usersTable;
    public function __construct($requestName, $mandatoryFields, $accountsTable, $usersTable, $user, $data)
    {
        $mandatoryFields[] = "session_id";
        $mandatoryFields[] = "user_id";

        parent::__construct($data, $mandatoryFields, $requestName);
        $this->accountsTable = $accountsTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
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