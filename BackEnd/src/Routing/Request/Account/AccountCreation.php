<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/13/2019
 * Time: 9:47 PM
 */
namespace BackEnd\Routing\Request\Account;
use BackEnd\Account\Account;
use BackEnd\Database\DBAccounts\CurrencyIDException;
use BackEnd\Routing\Request\PostRequest;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\User;

class AccountCreation extends PostRequest
{
    protected $name;
    protected $currentAmount;
    protected $currencyId;
    protected $sessionId;
    protected $userId;
    protected $user;

    protected $accountsTable;
    protected $usersTable;
    protected $currenciesTable;

    public function __construct($accountsTable, $usersTable, $user)
    {
        $this->accountsTable = $accountsTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function init(){
        parent::init();
        $missingParameters = array();

        if($this->name == ""){
            $missingParameters[] = "name";
        }
        if($this->currencyId == ""){
            $missingParameters[] = "currency_id";
        }
        if($this->currentAmount == ""){
            $missingParameters[] = "current_amount";
        }
        if($this->userId == ""){
            $missingParameters[] = "user_id";
        }
        if($this->sessionId == ""){
            $missingParameters[] = "session_id";
        }
        if(count($missingParameters) > 0){
            throw new MissingParametersException($missingParameters, "AccountCreation");
        }
    }

    /**
     * @return \BackEnd\Routing\Response\Account\AccountCreation
     * @throws InvalidSessionException
     */
    public function getResponse(){
        $this->user->connectWithSessionID($this->sessionId, $this->userId);
        if(!$this->user->isConnected()){
            throw new InvalidSessionException("AccountCreation");
        }
        $account = new Account(["name" => $this->name,
            "currency_id" => $this->currencyId,
            "current_amount" => $this->currentAmount,
            "user_id" => $this->userId]);
        return new \BackEnd\Routing\Response\Account\AccountCreation($this, $account);
    }

    public function getName(){
        return $this->name;
    }

    public function getCurrencyID(){
        return $this->currencyId;
    }

    public function getCurrentAmount(){
        return $this->currentAmount;
    }

    public function getUSerID(){
        return $this->userId;
    }

    public function getAccountsTable(){
        return $this->accountsTable;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }
}