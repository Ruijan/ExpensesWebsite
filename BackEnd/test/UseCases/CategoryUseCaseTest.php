<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 1:41 PM
 */

namespace BackEnd\Tests\UseCases;

use BackEnd\Routing\Request\Category\CategoryRequestFactory;
use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;

class CategoryUseCaseTest extends UseCaseTest
{
    /** @var CategoryRequestFactory */
    private $categoryRequestFactory;
    /** @var ConnectionRequestFactory */
    private $userRequestFactory;

    public function setUp(){
        parent::setUp();
        $this->categoryRequestFactory = new CategoryRequestFactory($this->db);
        $this->userRequestFactory = new ConnectionRequestFactory($this->db);
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
        $session = array('session_id' => $answerSignIn["DATA"]["SESSION_ID"],
            'user_id' => $answerSignIn["DATA"]["USER_ID"]);
        $answerCategoriesRetrieval = $this->getResponseFromRequest("RetrieveAll", $this->categoryRequestFactory, $session);
        $this->assertResponseStatus($answerCategoriesRetrieval);
        $this->assertEquals($answerCategoryCreation["DATA"], $answerCategoriesRetrieval["DATA"][0]);
        $answerUserDeletion = $this->getResponseFromRequest("Delete", $this->userRequestFactory, $user);
        $this->assertResponseStatus($answerUserDeletion);
    }
}
