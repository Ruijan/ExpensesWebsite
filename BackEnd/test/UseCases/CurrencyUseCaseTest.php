<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/17/2019
 * Time: 9:54 PM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Routing\Request\Currency\CurrencyRequestFactory;

use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;

class CurrencyUseCaseTest extends UseCaseTest
{
    /** @var CurrencyRequestFactory */
    private $currencyRequestFactory;
    /** @var ConnectionRequestFactory */
    private $userRequestFactory;

    public function setUp(){
        parent::setUp();
        $this->userRequestFactory = new ConnectionRequestFactory($this->db);
        $this->currencyRequestFactory = new CurrencyRequestFactory($this->db);
    }

    public function testPipelineExecution()
    {
        $currency = array("name" => "Dollar",
            "short_name" => "USD",
            'currency_dollars_change' => 1);
        $user = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");

        $answerSignUp = $this->getResponseFromRequest("SignUp", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerSignUp);
        $answerSignIn = $this->getResponseFromRequest("SignIn", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerSignIn);
        $currency = array_merge($currency, array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]));
        $answerCurrencyCreation = $this->getResponseFromRequest("Create", $this->currencyRequestFactory, $currency);
        $this->assertResponseStatus($answerCurrencyCreation);
        $answerCurrencyDeletion = $this->getResponseFromRequest("Delete", $this->currencyRequestFactory, $currency);
        $this->assertResponseStatus($answerCurrencyDeletion);
        $answerUserDeletion = $this->getResponseFromRequest("Delete", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerUserDeletion);
    }
}
