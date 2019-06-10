<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/8/2019
 * Time: 4:46 PM
 */

namespace BackEnd\Tests\UseCases;
use BackEnd\Application;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\DBTables;
use BackEnd\User;

class ConnectionUseCaseTest extends TestCase
{
    /** @var \BackEnd\Database\Database */
    private $db;
    public function setUp(){
        $app = new Application();
        $this->db = $app->getDatabase();
    }

    public function test__SignUp()
    {
        $data = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");
        $answer = $this->signUp($data);
        $this->deleteUser($data);
        if($answer["STATUS"] != "OK"){
            $this->assertEquals("", $answer["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $answer["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $answer["STATUS"]);
        }
    }

    public function test__SignIn(){
        $data = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");
        $this->signUp($data);
        $answer = $this->signIn($data);
        $this->deleteUser($data);
        if($answer["STATUS"] != "OK"){
            $this->assertEquals("", $answer["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $answer["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $answer["STATUS"]);
        }
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

