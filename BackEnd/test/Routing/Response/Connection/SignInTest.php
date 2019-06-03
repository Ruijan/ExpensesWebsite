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
    private $user;
    public function setUp()
    {
        $this->request = $this->getMockBuilder(\BackEnd\Routing\Request\Connection\SignIn::class)->disableOriginalConstructor()
            ->setMethods(['getEmail', 'getPassword'])->getMock();
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['areCredentialsValid', 'getUserFromEmail'])->getMock();
        $this->user = $this->getMockBuilder(\BackEnd\User::class)->disableOriginalConstructor()
            ->setMethods(['connect', 'isConnected', 'asDict'])->getMock();
    }

    public function test__construct()
    {
        $response = new SignIn($this->request, $this->usersTable, $this->user);
        $this->assertEquals($this->request, $response->getRequest());
        $this->assertEquals($this->usersTable, $response->getUsersTable());
    }

    public function testExecute()
    {
        $this->request->expects($this->any())
            ->method('getEmail')->with()->will($this->returnValue("email@test.com"));
        $this->request->expects($this->any())
            ->method('getPassword')->with()->will($this->returnValue("1j1j423jodwa"));
        $this->user->expects($this->once())
            ->method('connect')->with($this->usersTable, "email@test.com", "1j1j423jodwa");
        $this->user->expects($this->once())
            ->method('isConnected')->with()->will($this->returnValue(True));
        $this->user->expects($this->any())
            ->method('asDict')->with()->will(
                $this->returnValue(
                    array("ID" => 1,
                        "NAME" => "RECHEN",
                        "FIRST_NAME" => "Juju",
                        "EMAIL_VALIDATED" => true,
                        "EMAIL" => "email@test.com",
                        "SESSION_ID" => "1234567891234567")));
        $response = new SignIn($this->request, $this->usersTable, $this->user);
        $response->execute();
        $this->assertEquals(
            '{"STATUS":"OK","DATA":{"FIRST_NAME":"Juju","LAST_NAME":"RECHEN","USER_ID":1,"EMAIL_VALIDATED":true,"EMAIL":"email@test.com","SESSION_ID":"1234567891234567"}}',
            $response->getAnswer());
    }

    public function testExecuteWithInvalidPasswordShouldReturnError()
    {
        $this->request->expects($this->any())
            ->method('getEmail')->with()->will($this->returnValue("email@test.com"));
        $this->request->expects($this->any())
            ->method('getPassword')->with()->will($this->returnValue("1j1j423jodwa"));
        $this->user->expects($this->once())
            ->method('connect')->with($this->usersTable, "email@test.com", "1j1j423jodwa");
        $this->user->expects($this->once())
            ->method('isConnected')->with()->will($this->returnValue(False));
        $response = new SignIn($this->request, $this->usersTable, $this->user);
        $response->execute();
        $this->assertEquals('{"STATUS":"ERROR","ERROR_MESSAGE":"Email or password invalid"}', $response->getAnswer());
    }
}
