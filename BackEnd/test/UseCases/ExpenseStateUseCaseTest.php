<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 10:38 PM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Routing\Request\ExpenseState\DeleteExpenseState;
use BackEnd\Routing\Request\ExpenseState\RetrieveAllExpenseStates;
use PHPUnit\Framework\TestCase;
use BackEnd\Application;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\User;
use BackEnd\Routing\Request\ExpenseState\ExpenseStateCreation;

class ExpenseStateUseCaseTest extends TestCase
{
    /** @var Database */
    private $db;
    private $app;
    public function setUp(){
        $this->app = new Application();
        $this->db = $this->app->getDatabase();
    }

    public function testExpenseStatePipeline()
    {
        $user = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");


        $answerSignUp = $this->signUp($user);
        $this->assertEquals("OK", $answerSignUp["STATUS"]);
        $answerSignIn = $this->signIn($user);
        $this->assertEquals("OK", $answerSignIn["STATUS"]);
        $expenseState = array("name" => "Locked",
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerExpenseStateCreation = $this->createExpenseState($expenseState);
        $this->assertEquals("OK", $answerExpenseStateCreation["STATUS"]);
        $session = array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerExpenseStateRetrieval = $this->retrieveAllExpenseStates($session);
        $this->assertEquals("OK", $answerExpenseStateRetrieval["STATUS"]);
        $this->assertEquals($answerExpenseStateCreation["DATA"], $answerExpenseStateRetrieval["DATA"][0]);
        $expenseState["state_id"] = $answerExpenseStateCreation["DATA"]["ID"];
        $answerExpenseStateDeletion = $this->deleteExpenseState($expenseState);
        $this->assertEquals("OK", $answerExpenseStateDeletion["STATUS"]);
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

    public function createExpenseState($data){
        $request = new ExpenseStateCreation($this->db->getTableByName(DBTables::EXPENSES_STATES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function retrieveAllExpenseStates($data){
        $request = new RetrieveAllExpenseStates($this->db->getTableByName(DBTables::EXPENSES_STATES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function deleteExpenseState($data)
    {
        $request = new DeleteExpenseState(
            $this->db->getTableByName(DBTables::EXPENSES_STATES),
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
