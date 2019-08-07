<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/8/2019
 * Time: 4:46 PM
 */

namespace BackEnd\Tests\UseCases;
use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;

class ConnectionUseCaseTest extends UseCaseTest
{
    /** @var ConnectionRequestFactory */
    private $userRequestFactory;

    public function setUp(){
        parent::setUp();
        $this->userRequestFactory = new ConnectionRequestFactory($this->db);
    }

    public function testPipelineExecution()
    {
        $data = array("email" => "test@example.com",
            "password" => "12345678",
            "first_name" => "juju",
            "last_name" => "david");
        $answerSignUp = $this->getResponseFromRequest("SignUp", $this->userRequestFactory, $data);
        $this->assertResponseStatus($answerSignUp);
        $answerSignIn = $this->getResponseFromRequest("SignIn", $this->userRequestFactory, $data);
        $this->assertResponseStatus($answerSignIn);
        $answerUserDeletion = $this->getResponseFromRequest("Delete", $this->userRequestFactory, $data);
        $this->assertResponseStatus($answerUserDeletion);
    }
}

