<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 8:41 PM
 */
namespace BackEnd\Routing\Request\ExpenseState;

use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\Request;
use BackEnd\User;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\MissingParametersException;
use \BackEnd\Database\InsertionException;

class ExpenseStateCreation extends Request
{
    /** @var DBExpenseStates */
    protected $expenseStatesTable;
    /** @var DBUsers */
    protected $usersTable;
    /** @var User */
    protected $user;

    protected $sessionId;
    protected $userId;
    protected $name;
    public function __construct($expenseStatesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name", "session_id", "user_id"];
        parent::__construct($data, $mandatoryFields, "ExpenseStateCreation");
        $this->expenseStatesTable = $expenseStatesTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(): void{
        $this->response = [];
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $stateID = $this->expenseStatesTable->addState($this->name);
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array("ID" => $stateID, "NAME" => $this->name);
        }
        catch(MissingParametersException | InvalidSessionException |
        InsertionException | \Exception $exception){
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    public function getExpenseStatesTable(){
        return $this->expenseStatesTable;
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
            throw new InvalidSessionException($this->requestName);
        }
    }

}