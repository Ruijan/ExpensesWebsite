<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/24/2019
 * Time: 9:24 PM
 */

namespace BackEnd\Tests\UseCases;


use BackEnd\Routing\Request\Account\AccountRequestFactory;
use BackEnd\Routing\Request\Category\CategoryRequestFactory;
use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;
use BackEnd\Routing\Request\Currency\CurrencyRequestFactory;
use BackEnd\Routing\Request\Expense\ExpenseRequestFactory;
use BackEnd\Routing\Request\ExpenseState\ExpenseStateRequestFactory;
use BackEnd\Routing\Request\Payee\PayeeRequestFactory;
use BackEnd\Routing\Request\SubCategory\SubCategoryRequestFactory;

class ExpenseUseCaseTest extends UseCaseTest
{
    /** @var CurrencyRequestFactory */
    private $currencyRequestFactory;
    /** @var ConnectionRequestFactory */
    private $userRequestFactory;
    /** @var AccountRequestFactory */
    private $accountRequestFactory;
    /** @var ExpenseStateRequestFactory */
    private $expenseStateRequestFactory;
    /** @var SubCategoryRequestFactory */
    private $subCategoryRequestFactory;
    /** @var CategoryRequestFactory */
    private $categoryRequestFactory;
    /** @var PayeeRequestFactory */
    private $payeeRequestFactory;
    /** @var ExpenseRequestFactory */
    private $expenseRequestFactory;

    public function setUp(){
        parent::setUp();
        $this->userRequestFactory = new ConnectionRequestFactory($this->db);
        $this->currencyRequestFactory = new CurrencyRequestFactory($this->db);
        $this->accountRequestFactory = new AccountRequestFactory($this->db);
        $this->expenseStateRequestFactory = new ExpenseStateRequestFactory($this->db);
        $this->categoryRequestFactory = new CategoryRequestFactory($this->db);
        $this->subCategoryRequestFactory = new SubCategoryRequestFactory($this->db);
        $this->payeeRequestFactory = new PayeeRequestFactory($this->db);
        $this->expenseRequestFactory = new ExpenseRequestFactory($this->db);
    }

    public function testPipelineExecution()
    {
        $user = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");
        $answerSignUp = $this->getResponseFromRequest("SignUp", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerSignUp);
        $answerSignIn = $this->getResponseFromRequest("SignIn", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerSignIn);
        $currency = array("name" => "Dollar",
            "short_name" => "USD",
            'currency_dollars_change' => 1,
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerCurrencyCreation = $this->getResponseFromRequest("Create", $this->currencyRequestFactory, $currency);
        $this->assertResponseStatus($answerCurrencyCreation);
        $currency["currency_id"] = $answerCurrencyCreation["DATA"]["CURRENCY_ID"];
        $expenseState = array("name" => "Locked",
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerExpenseStateCreation = $this->getResponseFromRequest("Create", $this->expenseStateRequestFactory, $expenseState);
        $this->assertResponseStatus($answerExpenseStateCreation);
        $expenseState["state_id"] = $answerExpenseStateCreation["DATA"]["ID"];
        $category = array("name" => "Food",
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerCategoryCreation = $this->getResponseFromRequest("Create", $this->categoryRequestFactory, $category);
        $this->assertResponseStatus($answerCategoryCreation);
        $subCategory = array("name" => "Groceries",
            'parent_id' => $answerCategoryCreation["DATA"]["category_id"],
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerSubCategoryCreation = $this->getResponseFromRequest("Create", $this->subCategoryRequestFactory, $subCategory);
        $this->assertResponseStatus($answerSubCategoryCreation);
        $subCategory["id"] = $answerSubCategoryCreation["DATA"]["id"];
        $subCategory["category_id"] = $subCategory["parent_id"];
        $account = array('name' => 'Current',
            'currency_id' => $answerCurrencyCreation["DATA"]["CURRENCY_ID"],
            'current_amount' => 4061.68,
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerAccountCreation = $this->getResponseFromRequest("Create", $this->accountRequestFactory, $account);
        $this->assertResponseStatus($answerAccountCreation);
        $account["account_id"] = $answerAccountCreation["DATA"]["id"];
        $payee = array('name' => 'Migros',
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerPayeeCreation = $this->getResponseFromRequest("Create", $this->payeeRequestFactory, $payee);
        $this->assertResponseStatus($answerPayeeCreation);
        $payee["payee_id"] = $answerPayeeCreation["DATA"]["ID"];
        $expense = array("expense_date" => "2019-07-27 10:05:54",
            "location" => "Lausanne",
            "account_id" => $account["account_id"],
            "payee" => $payee["name"],
            "payee_id" => $answerPayeeCreation["DATA"]["ID"],
            "category" => $category["name"],
            "category_id" => $answerCategoryCreation["DATA"]["category_id"],
            "sub_category" => $subCategory["name"],
            "sub_category_id" => $answerSubCategoryCreation["DATA"]["id"],
            "amount" => 541.00,
            "currency" => $currency["name"],
            "currency_id" => $subCategory["id"],
            "state" => $expenseState["name"],
            "state_id" => $expenseState["state_id"],
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerExpenseCreation = $this->getResponseFromRequest("Create", $this->expenseRequestFactory, $expense);
        $this->assertResponseStatus($answerExpenseCreation);
        $expense["expense_id"] = $answerExpenseCreation["DATA"]["ID"];
        $answerExpenseRetrieval = $this->getResponseFromRequest("RetrieveAllFromAccount", $this->expenseRequestFactory, $expense);
        $this->assertResponseStatus($answerExpenseRetrieval);
        $expense["expense_id"] = $answerExpenseRetrieval["DATA"][0]["id"];
        $answerExpenseDeletion = $this->getResponseFromRequest("Delete", $this->expenseRequestFactory, $expense);
        $this->assertResponseStatus($answerExpenseDeletion);
        $answerPayeeDeletion = $this->getResponseFromRequest("Delete", $this->payeeRequestFactory, $payee);
        $this->assertResponseStatus($answerPayeeDeletion);
        $answerExpenseStateDeletion = $this->getResponseFromRequest("Delete", $this->expenseStateRequestFactory, $expenseState);
        $this->assertResponseStatus($answerExpenseStateDeletion);
        $answerAccountDeletion = $this->getResponseFromRequest("Delete", $this->accountRequestFactory, $account);
        $this->assertResponseStatus($answerAccountDeletion);
        $answerCurrencyDeletion = $this->getResponseFromRequest("Delete", $this->currencyRequestFactory, $currency);
        $this->assertResponseStatus($answerCurrencyDeletion);
        $answerSubCategoryDeletion = $this->getResponseFromRequest("Delete", $this->subCategoryRequestFactory, $subCategory);
        $this->assertResponseStatus($answerSubCategoryDeletion);
        $answerUserDeletion = $this->getResponseFromRequest("Delete", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerUserDeletion);

    }
}
