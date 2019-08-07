<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 6:19 PM
 */

namespace BackEnd\Routing\Request\Expense;
use BackEnd\Database\InsertionException;
use BackEnd\Expense;

class ExpenseCreation extends \BackEnd\Routing\Request\ConnectedRequest
{
    /** @var \BackEnd\Database\DBExpenses\DBExpenses */
    protected $expensesTable;
    protected $expense;
    protected $expenseDate;
    protected $location;
    protected $accountId;
    protected $payee;
    protected $payeeId;
    protected $category;
    protected $categoryId;
    protected $subCategory;
    protected $subCategoryId;
    protected $amount;
    protected $currency;
    protected $currencyId;
    protected $state;
    protected $stateId;

    public function __construct($expensesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = array_keys(Expense::$format);
        unset($mandatoryFields[0]);
        parent::__construct("ExpenseCreation",
            $mandatoryFields,
            $usersTable,
            $user,
            $data);

        $this->expensesTable = $expensesTable;
    }

    public function execute(): void{
        parent::execute();
        if($this->valid){
            $this->response = [];
            try{
                unset($this->data["session_id"]);
                unset($this->data["user_id"]);
                $this->expense = new Expense($this->data);
                $expenseID = $this->expensesTable->addExpense($this->expense);
                $this->response["STATUS"] = "OK";
                $this->response["DATA"] = array_merge(["ID" => $expenseID], $this->data);
            }
            catch(InsertionException $exception){
                $this->buildResponseFromException($exception);
            }
            $this->response = json_encode($this->response);
        }
    }

    public function getExpensesTable(){
        return $this->expensesTable;
    }
}