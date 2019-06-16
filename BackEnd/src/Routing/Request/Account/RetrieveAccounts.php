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

class RetrieveAccounts extends AccountRequest
{
    public function __construct($accountsTable, $usersTable, $user, $data)
    {
        parent::__construct("RetrieveAccounts", [],
            $accountsTable, $usersTable, $user, $data);
    }

    public function execute()
    {
        try {
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $accounts = $this->accountsTable->getAccountsFromUserID($this->userId);
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array();
            foreach ($accounts as $account) {
                $this->response["DATA"][] = $account->asDict();
            }
        } catch (MissingParametersException | InvalidSessionException $exception) {
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }
}