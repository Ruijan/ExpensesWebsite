<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/13/2019
 * Time: 9:47 PM
 */
namespace BackEnd\Routing\Request\Account;
use BackEnd\Account\Account;
use BackEnd\Database\DBAccounts\DBAccounts;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Database\DBUsers\InsertionException;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\User;
use BackEnd\Routing\Request\Request;
use BackEnd\Database\DBAccounts\AccountDuplicationException;
use BackEnd\Database\DBAccounts\CurrencyIDException;
use BackEnd\Database\DBAccounts\UserIDException;

class AccountCreation extends Request
{
    protected $name;
    protected $currentAmount;
    protected $currencyId;
    protected $sessionId;
    protected $userId;
    /** @var Account */
    protected $account;
    /** @var User */
    protected $user;
    /** @var DBAccounts */
    protected $accountsTable;
    /** @var DBUsers */
    protected $usersTable;
    /** @var string */
    protected $response;

    public function __construct($accountsTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name", "session_id", "user_id", "current_amount", "currency_id"];
        parent::__construct($data, $mandatoryFields, "AccountCreation");
        $this->accountsTable = $accountsTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(): void{
        $this->response = [];
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $this->tryAddingAccount();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = $this->account->asDict();
        }
        catch(MissingParametersException | InvalidSessionException |
        \BackEnd\Database\DBAccounts\InsertionException | AccountDuplicationException |
        CurrencyIDException | UserIDException $exception){
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
            throw new InvalidSessionException("AccountCreation");
        }
    }

    /**
     * @throws \BackEnd\Database\DBAccounts\AccountDuplicationException
     * @throws \BackEnd\Database\DBAccounts\CurrencyIDException
     * @throws \BackEnd\Database\DBAccounts\InsertionException
     * @throws \BackEnd\Database\DBAccounts\UserIDException
     */
    protected function tryAddingAccount(): void
    {
        $this->account = new Account(["name" => $this->name,
            "currency_id" => $this->currencyId,
            "current_amount" => $this->currentAmount,
            "user_id" => $this->userId]);
        $this->accountsTable->addAccount($this->account);
    }
}