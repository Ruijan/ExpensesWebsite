<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 10:38 PM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Application;
use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;
use BackEnd\Routing\Request\ExpenseState\ExpenseStateRequestFactory;

class ExpenseStateUseCaseTest extends UseCaseTest
{
    /** @var ConnectionRequestFactory */
    private $userRequestFactory;
    /** @var ExpenseStateRequestFactory */
    private $expenseStateRequestFactory;
    public function setUp(){
        parent::setUp();
        $this->userRequestFactory = new ConnectionRequestFactory($this->db);
        $this->expenseStateRequestFactory = new ExpenseStateRequestFactory($this->db);
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
        $expenseState = array("name" => "Locked",
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerExpenseStateCreation = $this->getResponseFromRequest("Create", $this->expenseStateRequestFactory, $expenseState);
        $this->assertResponseStatus($answerExpenseStateCreation);
        $session = array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerExpenseStateRetrieval = $this->getResponseFromRequest("RetrieveAll", $this->expenseStateRequestFactory, $session);
        $this->assertResponseStatus($answerExpenseStateRetrieval);
        $this->assertEquals($answerExpenseStateCreation["DATA"], $answerExpenseStateRetrieval["DATA"][0]);
        $expenseState["state_id"] = $answerExpenseStateCreation["DATA"]["ID"];
        $answerExpenseStateDeletion = $this->getResponseFromRequest("Delete", $this->expenseStateRequestFactory, $expenseState);
        $this->assertResponseStatus($answerExpenseStateDeletion);
        $answerUserDeletion = $this->getResponseFromRequest("Delete", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerUserDeletion);
    }
}
