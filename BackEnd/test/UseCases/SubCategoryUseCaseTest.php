<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/25/2019
 * Time: 6:36 PM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Application;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\Category\CategoryCreation;
use BackEnd\Routing\Request\SubCategory\DeleteSubCategory;
use BackEnd\Routing\Request\SubCategory\RetrieveAllSubCategories;
use BackEnd\Routing\Request\SubCategory\SubCategoryCreation;
use BackEnd\User;
use PHPUnit\Framework\TestCase;

class SubCategoryUseCaseTest extends TestCase
{
    /** @var Database */
    private $db;

    public function setUp()
    {
        $app = new Application();
        $this->db = $app->getDatabase();
    }

    public function testSubCategoryPipeline()
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
        $subCategory = array("name" => "Groceries",
            'parent_id' => $answerCategoryCreation["DATA"]["category_id"],
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerSubCategoryCreation = $this->createSubCategory($subCategory);
        $this->assertEquals("OK", $answerSubCategoryCreation["STATUS"]);
        $session = array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerSubCategoriesRetrieval = $this->retrieveAllSubCategories($session);
        $this->assertEquals("OK", $answerSubCategoriesRetrieval["STATUS"]);
        $this->assertEquals($answerSubCategoryCreation["DATA"], $answerSubCategoriesRetrieval["DATA"][0]);
        $subCategory["category_id"] = $answerSubCategoryCreation["DATA"]["id"];
        $answerSubCategoryDeletion = $this->deleteSubCategory($subCategory);
        $this->assertEquals("OK", $answerSubCategoryDeletion["STATUS"]);
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

    public function createCategory($data)
    {
        $request = new CategoryCreation($this->db->getTableByName(DBTables::CATEGORIES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function createSubCategory($data)
    {
        $request = new SubCategoryCreation($this->db->getTableByName(DBTables::SUBCATEGORIES),
            $this->db->getTableByName(DBTables::CATEGORIES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    public function retrieveAllSubCategories($data)
    {
        $request = new RetrieveAllSubCategories($this->db->getTableByName(DBTables::SUBCATEGORIES),
            $this->db->getTableByName(DBTables::USERS),
            new User(),
            $data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    private function deleteSubCategory($data)
    {
        $request = new DeleteSubCategory(
            $this->db->getTableByName(DBTables::SUBCATEGORIES),
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

    public function tearDown()
    {
        $this->db->dropDatabase();
    }
}