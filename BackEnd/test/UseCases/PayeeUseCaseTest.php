<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:54 AM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;
use BackEnd\Routing\Request\Payee\PayeeRequestFactory;

class PayeeUseCaseTest extends UseCaseTest
{
    /** @var ConnectionRequestFactory */
    private $userRequestFactory;
    /** @var PayeeRequestFactory */
    private $payeeRequestFactory;
    public function setUp()
    {
        parent::setUp();
        $this->userRequestFactory = new ConnectionRequestFactory($this->db);
        $this->payeeRequestFactory = new PayeeRequestFactory($this->db);
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
        $payee = array("name" => "Migros",
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerPayeeCreation = $this->getResponseFromRequest("Create", $this->payeeRequestFactory, $payee);
        $payee["payee_id"] = $answerPayeeCreation["DATA"]["ID"];
        $this->assertEquals("OK", $answerPayeeCreation["STATUS"]);
        $session = array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerPayeeRetrieval = $this->getResponseFromRequest("RetrieveAll", $this->payeeRequestFactory, $session);
        $this->assertEquals("OK", $answerPayeeRetrieval["STATUS"]);
        $this->assertEquals($payee["name"], $answerPayeeRetrieval["DATA"][0]["NAME"]);
        $this->assertEquals($payee["payee_id"], $answerPayeeRetrieval["DATA"][0]["ID"]);
        $answerPayeeDeletion = $this->getResponseFromRequest("Delete", $this->payeeRequestFactory, $payee);
        $this->assertResponseStatus($answerPayeeDeletion);
        $answerUserDeletion = $this->getResponseFromRequest("Delete", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerUserDeletion);
    }
}
