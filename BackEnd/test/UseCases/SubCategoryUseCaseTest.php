<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/25/2019
 * Time: 6:36 PM
 */

namespace BackEnd\Tests\UseCases;


use BackEnd\Routing\Request\Category\CategoryRequestFactory;
use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;
use BackEnd\Routing\Request\SubCategory\SubCategoryRequestFactory;

class SubCategoryUseCaseTest extends UseCaseTest
{
    /** @var ConnectionRequestFactory */
    private $userRequestFactory;
    /** @var SubCategoryRequestFactory */
    private $subCategoryRequestFactory;
    /** @var CategoryRequestFactory */
    private $categoryRequestFactory;
    public function setUp()
    {
        parent::setUp();
        $this->userRequestFactory = new ConnectionRequestFactory($this->db);
        $this->categoryRequestFactory = new CategoryRequestFactory($this->db);
        $this->subCategoryRequestFactory = new SubCategoryRequestFactory($this->db);
    }

    public function testPipelineExecution()
    {
        $user = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");

        $answerSignUp = $this->getResponseFromRequest("SignUp", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerSignUp);
        $answerSignIn = $this->getResponseFromRequest("SignIn", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerSignIn);
        $category = array("name" => "Food",
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerCategoryCreation = $this->getResponseFromRequest("Create", $this->categoryRequestFactory, $category);
        $this->assertResponseStatus($answerCategoryCreation);
        $subCategory = array("name" => "Groceries",
            'parent_id' => $answerCategoryCreation["DATA"]["category_id"],
            'session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerSubCategoryCreation = $this->getResponseFromRequest("Create", $this->subCategoryRequestFactory, $subCategory);
        $this->assertResponseStatus($answerSubCategoryCreation);
        $session = array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerSubCategoriesRetrieval= $this->getResponseFromRequest("RetrieveAll", $this->subCategoryRequestFactory, $session);
        $this->assertResponseStatus($answerSubCategoriesRetrieval);
        $this->assertEquals($answerSubCategoryCreation["DATA"], $answerSubCategoriesRetrieval["DATA"][0]);
        $subCategory["category_id"] = $answerSubCategoryCreation["DATA"]["id"];
        $answerSubCategoryDeletion = $this->getResponseFromRequest("Delete", $this->subCategoryRequestFactory, $subCategory);
        $this->assertResponseStatus($answerSubCategoryDeletion);
        $answerUserDeletion = $this->getResponseFromRequest("Delete", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerUserDeletion);
    }
}