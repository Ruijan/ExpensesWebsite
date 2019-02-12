<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/7/2019
 * Time: 11:15 PM
 */

use BackEnd\Routing\Request\Connection\SignIn;
use PHPUnit\Framework\TestCase;

class SignInTest extends TestCase
{
    private $usersTable;
    public function setUp()
    {
        $_POST = array("email" => "testEmail@gmail.com", "password" => "13464awd6a43123w");
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['areCredentialsValid'])->getMock();
    }

    public function testInitialization(){
        $signInRequest = new SignIn($this->usersTable);
        $signInRequest->init();
        $this->assertEquals($_POST["email"], $signInRequest->getEmail());
        $this->assertEquals($_POST["password"], $signInRequest->getPassword());
        $this->assertEquals($this->usersTable, $signInRequest->getUsersTable());
    }

    public function testInitializationWithMissingPasswordShouldThrow(){
        $_POST = array("email" => "testEmail@gmail.com");
        $signInRequest = new SignIn($this->usersTable);
        $this->expectException(\Backend\Routing\Request\Connection\MissingParametersException::class);
        $signInRequest->init();
    }
    public function testInitializationWithMissingEmailShouldThrow(){
        $_POST = array("password" => "dwa486413dwa");
        $signInRequest = new SignIn($this->usersTable);
        $this->expectException(\Backend\Routing\Request\Connection\MissingParametersException::class);
        $signInRequest->init();
    }

    public function testGetResponse(){
        $signInRequest = new SignIn($this->usersTable);
        $signInRequest->init();
        $response = $signInRequest->getResponse();
        $this->assertEquals(\BackEnd\Routing\Response\Connection\SignIn::class, get_class($response));
    }
}
