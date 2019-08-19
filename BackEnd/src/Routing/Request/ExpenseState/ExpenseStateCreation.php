<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 8:41 PM
 */

namespace BackEnd\Routing\Request\ExpenseState;

use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Routing\Request\ConnectedRequest;
use \BackEnd\Database\InsertionException;

class ExpenseStateCreation extends ConnectedRequest
{
    /** @var DBExpenseStates */
    protected $expenseStatesTable;
    protected $name;

    public function __construct($expenseStatesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name"];
        parent::__construct("ExpenseStateCreation", $mandatoryFields, $usersTable, $user, $data);
        $this->expenseStatesTable = $expenseStatesTable;
    }

    public function execute(): void
    {
        parent::execute();
        if ($this->valid) {
            $this->response = [];
            try {
                $stateID = $this->expenseStatesTable->addState($this->name);
                $this->response["STATUS"] = "OK";
                $this->response["DATA"] = array("ID" => $stateID, "NAME" => $this->name);
            } catch (InsertionException | \Exception $exception) {
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