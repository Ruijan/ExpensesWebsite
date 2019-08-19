<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/24/2019
 * Time: 7:36 PM
 */

namespace BackEnd\Routing\Request\Expense;


use BackEnd\Database\DBExpenses\DBExpenses;
use BackEnd\Database\DBExpenses\UndefinedExpenseException;
use BackEnd\Routing\Request\ConnectedRequest;

class DeleteExpense extends ConnectedRequest
{
    protected $expenseId;
    /** @var DBExpenses */
    protected $expensesTable;

    public function __construct($payeesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["expense_id"];
        parent::__construct("DeletePayee", $mandatoryFields, $usersTable, $user, $data);
        $this->expensesTable = $payeesTable;
    }

    public function execute()
    {
        parent::execute();
        if($this->valid){
            $this->response = [];
            try{
                $this->checkIfExpenseExists();
                $this->expensesTable->deleteExpense($this->expenseId);
                $this->response["STATUS"] = "OK";
            }catch(UndefinedExpenseException $exception){
                $this->buildResponseFromException($exception);
            }
            $this->response = json_encode($this->response);
        }
    }

    public function getExpensesTable(){
        return $this->expensesTable;
    }

    protected function checkIfExpenseExists(): void
    {
        if (!$this->expensesTable->doesExpenseIDExist($this->expenseId)) {
            throw new UndefinedExpenseException($this->expenseId);
        }
    }
}