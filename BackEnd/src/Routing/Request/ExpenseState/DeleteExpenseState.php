<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 9:14 PM
 */

namespace BackEnd\Routing\Request\ExpenseState;

use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Database\DBExpenseStates\UndefinedExpenseStateID;
use BackEnd\Routing\Request\ConnectedRequest;

class DeleteExpenseState extends ConnectedRequest
{
    /** @var DBExpenseStates */
    protected $expenseStatesTable;
    protected $stateId;

    public function __construct($expenseStatesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["state_id"];
        parent::__construct("DeleteExpenseState", $mandatoryFields, $usersTable, $user, $data);
        $this->expenseStatesTable = $expenseStatesTable;
    }

    public function execute()
    {
        parent::execute();
        if ($this->valid) {
            $this->response = [];
            try {
                $this->expenseStatesTable->deleteState($this->stateId);
                $this->response["STATUS"] = "OK";
            } catch (UndefinedExpenseStateID $exception) {
                $this->valid = false;
                $this->buildResponseFromException($exception);
            }
            $this->response = json_encode($this->response);
        }
    }

    public function getExpenseStatesTable()
    {
        return $this->expenseStatesTable;
    }
}