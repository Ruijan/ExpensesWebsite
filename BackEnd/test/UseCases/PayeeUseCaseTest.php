<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:54 AM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Routing\Request\Payee\DeletePayee;
use BackEnd\Routing\Request\Payee\PayeeCreation;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\DBTables;
use BackEnd\Application;
use BackEnd\User;

class PayeeUseCaseTest extends TestCase
{

    /** @var Database */
    private $db;
    private $app;

    public function setUp()
    {
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
        $payee = array("name" => "Migros",
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerPayeeCreation = $this->createPayee($payee);
        $this->assertEquals("OK", $answerPayeeCreation["STATUS"]);
        /*$session = array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerExpenseStateRetrieval = $this->retrieveAllExpenseStates($session);
        $this->assertEquals("OK", $answerExpenseStateRetrieval["STATUS"]);
        $this->assertEquals($answerExpenseStateCreation["DATA"], $answerExpenseStateRetrieval["DATA"][0]);*/
        $payee["payee_id"] = $answerPayeeCreation["DATA"]["ID"];
        $answerPayeeDeletion = $this->deletePayee($payee);
        $this->assertEquals("OK", $answerPayeeDeletion["STATUS"]);
        $answerUserDeletion = $this->deleteUser($user);
        $this->assertEquals("OK", $answerUserDeletion["STATUS"]);
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

    public function createPayee($data)
    {
        $request = new PayeeCreation($this->db->getTableByName(DBTables::PAYEES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function deletePayee($data)
    {
        $request = new DeletePayee(
            $this->db->getTableByName(DBTables::PAYEES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function deleteUser($data)
    {
        $request = new \BackEnd\Routing\Request\Connection\DeleteUser(
            $this->db->getTableByName(DBTables::USERS),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function retrieveAllExpenseStates($data)
    {
        $request = new RetrieveAllExpenseStates($this->db->getTableByName(DBTables::PAYEES),
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
