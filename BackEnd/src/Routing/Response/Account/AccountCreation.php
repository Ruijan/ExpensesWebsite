<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 5/14/2019
 * Time: 10:10 PM
 */

namespace BackEnd\Routing\Response\Account;

class AccountCreation
{
    protected $request;
    protected $accountsTable;
    protected $usersTable;
    protected $response;
    protected $account;

    public function __construct($request, $account)
    {
        $this->request = $request;
        $this->accountsTable = $request->getAccountsTable();
        $this->usersTable = $request->getUsersTable();
        $this->account = $account;
    }

    public function execute(){
        $this->usersTable->updateSession($this->request->getUserID());
        $this->accountsTable->addAccount($this->account);

        $this->response = json_encode(array(
            "STATUS" => "OK",
            "DATA" => $this->account->asDict()));
    }

    public function getRequest(){
        return $this->request;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    public function getAccountsTable(){
        return $this->accountsTable;
    }

    public function getAccount(){
        return $this->account;
    }

    public function getAnswer(){
        return $this->response;
    }
}