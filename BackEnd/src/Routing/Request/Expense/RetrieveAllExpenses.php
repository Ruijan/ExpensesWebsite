<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 8/5/2019
 * Time: 9:42 PM
 */

namespace BackEnd\Routing\Request\Expense;


use BackEnd\Database\DBExpenses\DBExpenses;
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\MissingParametersException;

class RetrieveAllExpenses extends ConnectedRequest
{
    /** @var DBExpenses */
    protected $expensesTable;
    protected $accountsTable;
    protected $accountId;

    public function __construct($expensesTable, $usersTable, $user, $data)
    {
        parent::__construct("RetrieveExpensesForAccount", ["account_id"], $usersTable, $user, $data);
        $this->expensesTable = $expensesTable;
    }

    public function execute()
    {
        try {
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $expenses = $this->expensesTable->getExpensesForAccountID($this->accountId);
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array();
            foreach ($expenses as $expense) {
                $this->response["DATA"][] = $expense->asArray();
            }
        } catch (MissingParametersException | InvalidSessionException $exception) {
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    public function getExpensesTable(){
        return $this->expensesTable;
    }
}