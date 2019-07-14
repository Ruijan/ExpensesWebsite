<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 9:14 PM
 */

namespace BackEnd\Routing\Request\ExpenseState;


use BackEnd\Routing\Request\Request;
use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Database\DBExpenseStates\UndefinedExpenseStateID;

class DeleteExpenseState extends Request
{
    /** @var DBExpenseStates */
    protected $expenseStatesTable;
    protected $stateId;

    public function __construct($expenseStatesTable, $data)
    {
        $mandatoryFields = ["state_id"];
        parent::__construct($data, $mandatoryFields, "DeleteExpenseState");
        $this->expenseStatesTable = $expenseStatesTable;
    }

    public function execute()
    {
        try {
            $this->checkRequiredParameters();
            $this->expenseStatesTable->deleteState($this->stateId);
            $this->response["STATUS"] = "OK";
        } catch (MissingParametersException | UndefinedExpenseStateID $exception) {
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    public function getExpenseStatesTable()
    {
        return $this->expenseStatesTable;
    }
}