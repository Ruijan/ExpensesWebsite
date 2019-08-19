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
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;

abstract class AccountRequest extends ConnectedRequest
{
    /** @var DBAccounts */
    protected $accountsTable;
    public function __construct($requestName, $mandatoryFields, $accountsTable, $usersTable, $user, $data)
    {
        parent::__construct($requestName, $mandatoryFields, $usersTable, $user, $data);
        $this->accountsTable = $accountsTable;
    }

    public function getAccountsTable(){
        return $this->accountsTable;
    }
}