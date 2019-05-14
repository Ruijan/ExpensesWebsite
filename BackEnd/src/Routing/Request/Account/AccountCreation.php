<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/13/2019
 * Time: 9:47 PM
 */
namespace BackEnd\Routing\Request\Account;
use BackEnd\Routing\Request\PostRequest;
use BackEnd\Routing\Request\MissingParametersException;
use mysql_xdevapi\Exception;

class AccountCreation extends PostRequest
{
    protected $name;
    protected $currency;
    protected $currentAmount;
    protected $currencyID;
    protected $userKey;
    protected $accountsTable;
    protected $usersTable;

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
        if($this->currency == ""){
            $missingParameters[] = "currency";
        }
        if($this->currentAmount == ""){
            $missingParameters[] = "current_amount";
        }
        if($this->userKey == ""){
            $missingParameters[] = "user_key";
        }
        if(count($missingParameters) > 0){
            throw new MissingParametersException($missingParameters, "AccountCreation");
        }
    }

    public function getResponse(){
        if(!$this->usersTable->isUserSessionKeyValid($this->userKey)){
            throw new Exception("User not valid. User is not connected or has been away for too long.");
        }
        return new \BackEnd\Routing\Response\Account\AccountCreation();
    }

    public function getName(){
        return $this->name;
    }

    public function getCurrency(){
        return $this->currency;
    }

    public function getCurrentAmount(){
        return $this->currentAmount;
    }

    public function getUSerKey(){
        return $this->userKey;
    }

    public function getAccountsTable(){
        return $this->accountsTable;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }
}