<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/16/2019
 * Time: 7:04 PM
 */

use BackEnd\Routing\Request\Connection\SignUp;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\DBUsers\DBUsers;

class SignUpTest extends TestCase
{
    private $usersTable;
    public function setUp()
    {
        $_POST = array("email" => "testEmail@gmail.com",
            "password" => "13464awd6a43123w",
            "last_name" => "testName",
            "first_name" => "testFirstName");
        $this->usersTable = $this->getMockBuilder(DBUsers::class)->disableOriginalConstructor()->getMock();
    }

    public function testInitialization(){
        $signUpRequest = new SignUp($this->usersTable);
        $registeredDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $registeredFormatedDate = $registeredDate->format("Y-m-d H:i:s");
        $lastConnection = $registeredFormatedDate;
        $signUpRequest->init();
        $this->assertEquals($_POST["email"], $signUpRequest->getEmail());
        $this->assertEquals($_POST["password"], $signUpRequest->getPassword());
        $this->assertEquals($_POST["last_name"], $signUpRequest->getLastName());
        $this->assertEquals($_POST["first_name"], $signUpRequest->getFirstName());
        $this->assertEquals($registeredFormatedDate, $signUpRequest->getRegisteredDate());
        $this->assertEquals($lastConnection, $signUpRequest->getLastConnection());
        $this->assertTrue($registeredDate->getTimestamp() - $signUpRequest->getValidationID() < 10);
        $this->assertEquals($this->usersTable, $signUpRequest->getUsersTable());
    }

    public function testInitializationWithMissingPasswordShouldThrow(){
        unset($_POST["password"]);
        $signUpRequest = new SignUp($this->usersTable);
        $this->expectException(\Backend\Routing\Request\MissingParametersException::class);
        $signUpRequest->init();
    }
    public function testInitializationWithMissingEmailShouldThrow(){
        unset($_POST["email"]);
        $signUpRequest = new SignUp($this->usersTable);
        $this->expectException(\BackEnd\Routing\Request\MissingParametersException::class);
        $signUpRequest->init();
    }
    public function testInitializationWithMissingFirstNameShouldThrow(){
        unset($_POST["first_name"]);
        $signUpRequest = new SignUp($this->usersTable);
        $this->expectException(\Backend\Routing\Request\MissingParametersException::class);
        $signUpRequest->init();
    }
    public function testInitializationWithMissingLastNameShouldThrow(){
        unset($_POST["last_name"]);
        $signUpRequest = new SignUp($this->usersTable);
        $this->expectException(\Backend\Routing\Request\MissingParametersException::class);
        $signUpRequest->init();
    }

    public function testGetResponse(){
        $signUpRequest = new SignUp($this->usersTable);
        $signUpRequest->init();
        $response = $signUpRequest->getResponse();
        $this->assertEquals(\BackEnd\Routing\Response\Connection\SignUp::class, get_class($response));
    }
}
