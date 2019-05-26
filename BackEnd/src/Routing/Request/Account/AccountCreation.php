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

class AccountCreation extends PostRequest
{
    protected $name;
    protected $currentAmount;
    protected $currencyId;
    protected $userKey;
    protected $userId;

    protected $accountsTable;
    protected $usersTable;
    protected $currenciesTable;

    public function __construct($accountsTable, $usersTable)
    {
        $this->accountsTable = $accountsTable;
        $this->usersTable = $usersTable;
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
        if($this->userKey == ""){
            $missingParameters[] = "user_key";
        }
        if($this->userId == ""){
            $missingParameters[] = "user_id";
        }
        if(count($missingParameters) > 0){
            throw new MissingParametersException($missingParameters, "AccountCreation");
        }
    }

    public function getResponse(){
        if(!$this->usersTable->isUserSessionKeyValid($this->userKey)){
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

    public function getUSerKey(){
        return $this->userKey;
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