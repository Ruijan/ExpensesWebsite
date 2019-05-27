<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/3/2019
 * Time: 8:08 PM
 */

namespace BackEnd\Tests\Routing\Response\Connection;

use BackEnd\Routing\Response\Connection\SignIn;
use PHPUnit\Framework\TestCase;

class SignInTest extends TestCase
{
    private $request;
    private $usersTable;
    public function setUp()
    {
        $this->request = $this->getMockBuilder(\BackEnd\Routing\Request\Connection\SignIn::class)->disableOriginalConstructor()
            ->setMethods(['getEmail', 'getPassword'])->getMock();
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['areCredentialsValid', 'getUserFromEmail'])->getMock();
    }

    public function test__construct()
    {
        $response = new SignIn($this->request, $this->usersTable);
        $this->assertEquals($this->request, $response->getRequest());
        $this->assertEquals($this->usersTable, $response->getUsersTable());
    }

    public function testExecute()
    {
        $this->request->expects($this->any())
            ->method('getEmail')->with()->will($this->returnValue("email@test.com"));
        $this->request->expects($this->any())
            ->method('getPassword')->with()->will($this->returnValue("1j1j423jodwa"));
        $this->usersTable->expects($this->once())
            ->method('areCredentialsValid')->with("email@test.com", "1j1j423jodwa")->will($this->returnValue(True));
        $this->usersTable->expects($this->once())
            ->method('getUserFromEmail')->with("email@test.com")->will(
                $this->returnValue(
                    array("ID" => 1,
                        "NAME" => "RECHEN",
                        "FIRST_NAME" => "Juju",
                        "EMAIL_VALIDATED" => true,
                        "EMAIL" => "email@test.com")));
        $response = new SignIn($this->request, $this->usersTable);
        $response->execute();
        $this->assertEquals('{"Status":"OK","Data":{"first_name":"Juju","last_name":"RECHEN","user_ID":1,"email_validated":true,"email":"email@test.com"}}', $response->getAnswer());
    }

    public function testExecuteWithInvalidPasswordShouldReturnError()
    {
        $this->request->expects($this->any())
            ->method('getEmail')->with()->will($this->returnValue("email@test.com"));
        $this->request->expects($this->any())
            ->method('getPassword')->with()->will($this->returnValue("1j1j423jodwa"));
        $this->usersTable->expects($this->once())
            ->method('areCredentialsValid')->with("email@test.com", "1j1j423jodwa")->will($this->returnValue(false));
        $response = new SignIn($this->request, $this->usersTable);
        $response->execute();
        $this->assertEquals('{"Status":"ERROR","ERROR_MESSAGE":"Email or password invalid"}', $response->getAnswer());
    }
}
