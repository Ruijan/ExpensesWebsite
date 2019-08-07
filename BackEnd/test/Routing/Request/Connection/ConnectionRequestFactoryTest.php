<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 9:27 PM
 */

namespace Request;

use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\Connection\SignUp;
use BackEnd\Routing\Request\Connection\ConnectionRequestFactory;
use BackEnd\Routing\Request\Connection\SignIn;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\Database;

class ConnectionRequestFactoryTest extends TestCase
{
    private $database;
    public function setUp()
    {
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()
            ->setMethods(["getDriver", "getTableByName"])->getMock();
    }

    public function test__construct(){
        $factory = new ConnectionRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateSignInRequest(){
        $this->database->expects($this->once())
            ->method('getTableByName')
            ->with(DBTables::USERS);
        $factory = new ConnectionRequestFactory($this->database);
        $request = $factory->createRequest("SignIn", array());
        $this->assertEquals(SignIn::class, get_class($request));
    }

    public function testCreateSignUpRequest(){
        $this->database->expects($this->once())
            ->method('getTableByName')
            ->with(DBTables::USERS);
        $factory = new ConnectionRequestFactory($this->database);
        $request = $factory->createRequest("SignUp", array());
        $this->assertEquals(SignUp::class, get_class($request));
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new ConnectionRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut", array());
    }
}
