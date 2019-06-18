<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 1:41 PM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Routing\Request\Category\SubCategoryCreation;
use PHPUnit\Framework\TestCase;
use BackEnd\Application;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\User;

class CategoryUseCaseTest extends TestCase
{
    /** @var Database */
    private $db;

    public function setUp(){
        $app = new Application();
        $this->db = $app->getDatabase();
    }

    public function testAccountPipeline()
    {
        $user = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");


        $answerSignUp = $this->signUp($user);
        $this->assertEquals("OK", $answerSignUp["STATUS"]);
        $answerSignIn = $this->signIn($user);
        $this->assertEquals("OK", $answerSignIn["STATUS"]);
        $category = array("name" => "Food",
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerCategoryCreation = $this->createCategory($category);
        $this->assertEquals("OK", $answerCategoryCreation["STATUS"]);
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

    public function createCategory($data){
        $request = new SubCategoryCreation($this->db->getTableByName(DBTables::CATEGORIES),
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
