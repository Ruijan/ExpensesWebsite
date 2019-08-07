<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/8/2019
 * Time: 6:34 PM
 */

namespace BackEnd\Tests\UseCases;
use BackEnd\Routing\Request\Account\AccountRequestFactory;
use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;
use BackEnd\Routing\Request\Currency\CurrencyRequestFactory;


class AccountUseCaseTest extends UseCaseTest
{

    /** @var AccountRequestFactory */
    private $accountRequestFactory;
    /** @var ConnectionRequestFactory */
    private $userRequestFactory;
    /** @var CurrencyRequestFactory */
    private $currencyRequestFactory;

    public function setUp(){
        parent::setUp();
        $this->accountRequestFactory = new AccountRequestFactory($this->db);
        $this->userRequestFactory = new ConnectionRequestFactory($this->db);
        $this->currencyRequestFactory = new CurrencyRequestFactory($this->db);
    }

    public function testPipelineExecution(){
        $user = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");


        $answerSignUp = $this->getResponseFromRequest("SignUp", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerSignUp);
        $answerSignIn = $this->getResponseFromRequest("SignIn", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerSignIn);
        $currency = array("name" => "euro",
            "short_name" => "EUR",
            "currency_dollars_change" => 1.12,
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerCurrencyCreation = $this->getResponseFromRequest("Create", $this->currencyRequestFactory, $currency);
        $this->assertResponseStatus($answerCurrencyCreation);
        $account = array('name' => 'Current',
            'currency_id' => $answerCurrencyCreation["DATA"]["CURRENCY_ID"],
            'current_amount' => 4061.68,
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);

        $answerAccountCreation = $this->getResponseFromRequest("Create", $this->accountRequestFactory, $account);
        $this->assertResponseStatus($answerAccountCreation);
        $condensedUser = array(
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerRetrieval = $this->getResponseFromRequest("Retrieve", $this->accountRequestFactory, $condensedUser);
        $this->assertResponseStatus($answerRetrieval);
        $this->assertEquals($account["name"], $answerRetrieval["DATA"][0]["name"]);
        $answerAccountDeletion = $this->getResponseFromRequest("Delete", $this->accountRequestFactory, $account);
        $this->assertResponseStatus($answerAccountDeletion);
        $answerCurrencyDeletion = $this->getResponseFromRequest("Delete", $this->currencyRequestFactory, $currency);
        $this->assertResponseStatus($answerCurrencyDeletion);
        $answerUserDeletion = $this->getResponseFromRequest("Delete", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerUserDeletion);
    }
}