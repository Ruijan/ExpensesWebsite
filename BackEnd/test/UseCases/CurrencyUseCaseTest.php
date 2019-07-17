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

    public function testAccountPipeline()
    {
        $currency = array("name" => "Dollar",
            "short_name" => "USD",
            'currency_dollars_change' => 1);
        $answerCurrencyCreation = $this->createCurrency($currency);
        $this->assertEquals("OK", $answerCurrencyCreation["STATUS"]);
        $answerCurrencyDeletion = $this->deleteCurrency($currency);
        $this->assertEquals("OK", $answerCurrencyDeletion["STATUS"]);
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

    public function createCurrency($data){
        $request = new CurrencyCreation($this->db->getTableByName(DBTables::CURRENCIES),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function retrieveAllCurrencies($data){
        $request = new RetrieveAllCategories($this->db->getTableByName(DBTables::CATEGORIES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function deleteCurrency($data){
        $request = new DeleteCurrency(
            $this->db->getTableByName(DBTables::CURRENCIES),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function tearDown()
    {
        $this->db->dropDatabase();
    }
}
