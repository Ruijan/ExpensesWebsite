<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/11/2019
 * Time: 8:02 PM
 */

namespace BackEnd\Tests\Routing;

use BackEnd\Routing\Router;
use PHPUnit\Framework\TestCase;
use BackEnd\Routing\Request\Connection\SignIn;
use BackEnd\Routing\Request\ConnectionRequestFactory;
use BackEnd\Routing\ServerProperties;

class RouterTest extends TestCase
{
    private $serverProperties;
    private $factories;
    private $router;
    private $request;
    private $response;

    public function setUp()
    {
        $this->serverProperties = $this->getMockBuilder(ServerProperties::class)
            ->disableOriginalConstructor()->setMethods(['getURI'])->getMock();
        $connectionRequestFactory = $this->getMockBuilder(ConnectionRequestFactory::class)
            ->disableOriginalConstructor()->setMethods(['createRequest'])->getMock();
        $this->request = $this->getMockBuilder(SignIn::class)
            ->disableOriginalConstructor()->setMethods(['init', 'getResponse'])->getMock();
        $this->response = $this->getMockBuilder(BackEnd\Routing\Response\Connection\SignIn::class)
            ->disableOriginalConstructor()->setMethods(['execute'])->getMock();
        $this->factories = array("connection" => $connectionRequestFactory);
    }

    public function testResolveRoute()
    {
        $this->serverProperties->expects($this->once())->method('getURI')
            ->with()->will($this->returnValue("connection/signIn"));
        $this->factories["connection"]->expects($this->once())->method('createRequest')
            ->with()->will($this->returnValue($this->request));
        $this->request->expects($this->once())->method('init');
        $this->request->expects($this->once())->method('getResponse')
            ->with()->will($this->returnValue($this->response));
        $this->response->expects($this->once())->method('execute');
        $this->router = new Router($this->serverProperties, $this->factories);
        $this->router->resolveRoute();
    }

    public function testResolveEmptyRouteShouldThrow()
    {
        $this->serverProperties->expects($this->once())->method('getURI')
            ->with()->will($this->returnValue(""));
        $this->expectException(\InvalidArgumentException::class);
        $this->router = new Router($this->serverProperties, $this->factories);
        $this->router->resolveRoute();
    }

    public function testResolveWrongRouteShouldThrow()
    {
        $this->serverProperties->expects($this->once())->method('getURI')
            ->with()->will($this->returnValue("wrongRoute/signIn"));
        $this->expectException(\InvalidArgumentException::class);
        $this->router = new Router($this->serverProperties, $this->factories);
        $this->router->resolveRoute();
    }

    public function test__construct()
    {
        $this->router = new Router($this->serverProperties, $this->factories);
        $this->assertEquals($this->serverProperties, $this->router->getServerProperties());
        $this->assertEquals($this->factories, $this->router->getRequestFactories());
    }
}
