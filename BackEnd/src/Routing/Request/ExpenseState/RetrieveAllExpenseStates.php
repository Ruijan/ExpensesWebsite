<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 9:13 PM
 */

namespace BackEnd\Routing\Request\ExpenseState;


use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\Request;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\MissingParametersException;

class RetrieveAllExpenseStates extends Request
{
    protected $sessionId;
    protected $userId;

    /** @var \BackEnd\User */
    protected $user;
    /** @var DBExpenseStates */
    protected $expenseStatesTable;
    /** @var DBUsers */
    protected $usersTable;

    public function __construct($subCategoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["session_id", "user_id"];
        parent::__construct($data, $mandatoryFields, "RetrieveExpenseStates");
        $this->expenseStatesTable = $subCategoriesTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(){
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $this->response["DATA"] = $this->expenseStatesTable->getAllExpenseStates();
            $this->response["STATUS"] = "OK";
        }
        catch(MissingParametersException | InvalidSessionException $exception){
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    public function getExpenseStatesTable(){
        return $this->expenseStatesTable;
    }
    /**
     * @throws InvalidSessionException
     */
    protected function tryConnectingUser(): void
    {
        $this->user->connectWithSessionID($this->usersTable, $this->sessionId, $this->userId);
        if (!$this->user->isConnected()) {
            throw new InvalidSessionException($this->requestName);
        }
    }
}