<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/17/2019
 * Time: 9:54 PM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Routing\Request\Currency\CurrencyCreation;
use BackEnd\Routing\Request\Currency\DeleteCurrency;
use BackEnd\Routing\Request\Currency\RetrieveAllCurrencies;
use PHPUnit\Framework\TestCase;
use BackEnd\Application;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\User;

class CurrencyUseCaseTest extends TestCase
{
    /** @var Database */
    private $db;

    public function setUp(){
        $app = new Application();
        $this->db = $app->getDatabase();
    }

    public function testCurrencyPipeline()
    {
        $currency = array("name" => "Dollar",
            "short_name" => "USD",
            'currency_dollars_change' => 1);
        $user = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");

        $answerSignUp = $this->signUp($user);
        $this->assertEquals("OK", $answerSignUp["STATUS"]);
        $answerSignIn = $this->signIn($user);
        $this->assertEquals("OK", $answerSignIn["STATUS"]);
        $currency = array_merge($currency, array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]));
        $answerCurrencyCreation = $this->createCurrency($currency);
        $this->assertEquals("OK", $answerCurrencyCreation["STATUS"]);
        $answerCurrencyDeletion = $this->deleteCurrency($currency);
        $this->assertEquals("OK", $answerCurrencyDeletion["STATUS"]);
    }

    private function signUp($data)
    {
        $request = new \BackEnd\Routing\Request\Connection\SignUp(
            $this->db->getTableByName(DBTables::USERS),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function signIn($data)
    {
        $request = new \BackEnd\Routing\Request\Connection\SignIn(
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }


    public function createCurrency($data){
        $request = new CurrencyCreation($this->db->getTableByName(DBTables::CURRENCIES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function retrieveAllCurrencies($data){
        $request = new RetrieveAllCurrencies($this->db->getTableByName(DBTables::CURRENCIES),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function deleteCurrency($data){
        $request = new DeleteCurrency(
            $this->db->getTableByName(DBTables::CURRENCIES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function tearDown()
    {
        $this->db->dropDatabase();
    }
}
