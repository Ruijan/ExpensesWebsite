<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 9:27 PM
 */

namespace Request;

use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\Request\Connection\SignIn;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\Database;

class RequestFactoryTest extends TestCase
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

    public function testCreateRequest(){
        $this->database->expects($this->once())
            ->method('getTableByName')
            ->with("dbuser");
        $factory = new ConnectionRequestFactory($this->database);
        $request = $factory->createRequest("SignIn");
        $this->assertEquals(SignIn::class, get_class($request));
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new ConnectionRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut");
    }
}
