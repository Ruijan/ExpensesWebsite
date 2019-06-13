<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/8/2019
 * Time: 6:34 PM
 */

namespace BackEnd\Tests\UseCases;
use BackEnd\Application;
use BackEnd\Database\Database;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\DBTables;
use BackEnd\User;

class AccountUseCaseTest extends TestCase
{
    /** @var Database */
    private $db;

    public function setUp(){
        $app = new Application();
        $this->db = $app->getDatabase();
    }

    public function testAccountPipeline(){
        $user = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");


        $answerSignUp = $this->signUp($user);
        $this->assertEquals("OK", $answerSignUp["STATUS"]);
        $answerSignIn = $this->signIn($user);
        $this->assertEquals("OK", $answerSignIn["STATUS"]);
        $currency = array("name" => "euro",
            "short_name" => "EUR",
            "currency_dollars_change" => 1.12);
        $answerCurrencyCreation = $this->createCurrency($currency);
        $this->assertEquals("OK", $answerCurrencyCreation["STATUS"]);
        $account = array('name' => 'Current',
            'currency_id' => $answerCurrencyCreation["DATA"]["CURRENCY_ID"],
            'current_amount' => 4061.68,
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);

        $answerAccountCreation = $this->createAccount($account);
        $this->assertEquals("OK", $answerAccountCreation["STATUS"]);
        $condensedUser = array(
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerRetrieval = $this->retrieveAccounts($condensedUser);
        $this->assertEquals("OK", $answerRetrieval["STATUS"]);
        $this->assertEquals($account["name"], $answerRetrieval["DATA"][0]["name"]);
        $answerAccountDeletion = $this->deleteAccount($account);
        $this->assertEquals("OK", $answerAccountDeletion["STATUS"]);
        $answerUserDeletion = $this->deleteUser($user);
        $this->assertEquals("OK", $answerUserDeletion["STATUS"]);
    }

    private function signUp($data){
        $request = new \BackEnd\Routing\Request\Connection\SignUp(
            $this->db->getTableByName(DBTables::USERS),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function signIn($data){
        $request = new \BackEnd\Routing\Request\Connection\SignIn(
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function createCurrency($data){
        $request = new \BackEnd\Routing\Request\Currency\CurrencyCreation(
            $this->db->getTableByName(DBTables::CURRENCIES),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function createAccount($data){
        $request = new \BackEnd\Routing\Request\Account\AccountCreation(
            $this->db->getTableByName(DBTables::ACCOUNTS),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function retrieveAccounts($data){
        $request = new \BackEnd\Routing\Request\Account\RetrieveAccounts(
            $this->db->getTableByName(DBTables::ACCOUNTS),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function deleteAccount($data){
        $request = new \BackEnd\Routing\Request\Account\DeleteAccount(
            $this->db->getTableByName(DBTables::ACCOUNTS),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function deleteUser($data){
        $request = new \BackEnd\Routing\Request\Connection\DeleteUser(
            $this->db->getTableByName(DBTables::USERS),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function tearDown()
    {
        $this->db->dropDatabase();
    }
}