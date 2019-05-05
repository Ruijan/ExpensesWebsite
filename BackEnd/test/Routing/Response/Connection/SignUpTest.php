<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/16/2019
 * Time: 7:48 PM
 */

namespace BackEnd\Tests\Routing\Response\Connection;

use BackEnd\Routing\Response\Connection\SignUp;
use PHPUnit\Framework\TestCase;

class SignUpTest extends TestCase
{
    private $request;
    private $usersTable;
    public function setUp()
    {
        $this->request = $this->getMockBuilder(\BackEnd\Routing\Request\Connection\SignUp::class)->disableOriginalConstructor()
            ->setMethods(['getEmail', 'getPassword', 'getValidationID', 'getLastConnection', 'getRegisteredDate', 'getFirstName', 'getLastName'])->getMock();
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['addUser', 'checkIfEmailExists'])->getMock();
    }

    public function test__construct()
    {
        $response = new SignUp($this->request, $this->usersTable);
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
            ->method('checkIfEmailExists')->with("email@test.com")->will($this->returnValue(False));
        $this->usersTable->expects($this->once())->method('addUser');
        $response = new SignUp($this->request, $this->usersTable);
        $response->execute();
        $this->assertEquals('Signed Up', $response->getAnswer());
    }
}
