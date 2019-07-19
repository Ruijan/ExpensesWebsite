<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 9:13 PM
 */

namespace BackEnd\Routing\Request\ExpenseState;


use BackEnd\Database\DBExpenseStates\DBExpenseStates;
use BackEnd\Routing\Request\ConnectedRequest;

class RetrieveAllExpenseStates extends ConnectedRequest
{
    /** @var DBExpenseStates */
    protected $expenseStatesTable;

    public function __construct($subCategoriesTable, $usersTable, $user, $data)
    {
        parent::__construct("RetrieveExpenseStates", [], $usersTable, $user, $data);
        $this->expenseStatesTable = $subCategoriesTable;
    }

    public function execute()
    {
        parent::execute();
        if ($this->valid) {
            $this->response = [];
            $this->response["DATA"] = $this->expenseStatesTable->getAllExpenseStates();
            $this->response["STATUS"] = "OK";
            $this->response = json_encode($this->response);
        }
    }

    public function getExpenseStatesTable()
    {
        return $this->expenseStatesTable;
    }
}