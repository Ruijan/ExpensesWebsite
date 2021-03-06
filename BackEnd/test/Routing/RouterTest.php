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

    public function setUp()
    {
        $this->serverProperties = $this->getMockBuilder(ServerProperties::class)
            ->disableOriginalConstructor()->setMethods(['getURI', 'getDocumentRoot', 'getCurrentFolder'])->getMock();
        $connectionRequestFactory = $this->getMockBuilder(ConnectionRequestFactory::class)
            ->disableOriginalConstructor()->setMethods(['createRequest'])->getMock();
        $this->request = $this->getMockBuilder(SignIn::class)
            ->disableOriginalConstructor()->setMethods(['execute', 'getResponse'])->getMock();
        $this->factories = array("connection" => $connectionRequestFactory);
    }

    public function testResolveRoute()
    {
        $this->serverProperties->expects($this->once())->method('getURI')
            ->with()->will($this->returnValue("/Expenses/Website/connection/signIn"));
        $this->serverProperties->expects($this->once())->method('getDocumentRoot')
            ->with()->will($this->returnValue("C:/wamp64/www"));
        $this->serverProperties->expects($this->once())->method('getCurrentFolder')
            ->with()->will($this->returnValue("C:\wamp64\www\Expenses\Website"));
        $this->factories["connection"]->expects($this->once())->method('createRequest')
            ->with()->will($this->returnValue($this->request));
        $this->request->expects($this->once())->method('execute');
        $this->router = new Router($this->serverProperties, $this->factories);
        $this->router->resolveRoute();
    }

    public function testResolveRouteWithActionInURL()
    {
        $this->serverProperties->expects($this->once())->method('getURI')
            ->with()->will($this->returnValue("/Expenses/Website/?action=connection/signIn"));
        $this->factories["connection"]->expects($this->once())->method('createRequest')
            ->with()->will($this->returnValue($this->request));
        $this->request->expects($this->once())->method('execute');
        $this->router = new Router($this->serverProperties, $this->factories);
        $this->router->resolveRoute();
    }

    public function testResolveEmptyRouteShouldThrow()
    {
        $this->serverProperties->expects($this->once())->method('getURI')
            ->with()->will($this->returnValue(""));
        $this->serverProperties->expects($this->once())->method('getDocumentRoot')
            ->with()->will($this->returnValue("C:/wamp64/www"));
        $this->serverProperties->expects($this->once())->method('getCurrentFolder')
            ->with()->will($this->returnValue("C:\wamp64\www\Expenses\Website"));
        $this->expectException(\InvalidArgumentException::class);
        $this->router = new Router($this->serverProperties, $this->factories);
        $this->router->resolveRoute();
    }

    public function testResolveWrongRouteShouldThrow()
    {
        $this->serverProperties->expects($this->once())->method('getURI')
            ->with()->will($this->returnValue("/wrongRoute/signIn"));
        $this->serverProperties->expects($this->once())->method('getDocumentRoot')
            ->with()->will($this->returnValue("C:/wamp64/www"));
        $this->serverProperties->expects($this->once())->method('getCurrentFolder')
            ->with()->will($this->returnValue("C:\wamp64\www\Expenses\Website"));
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

    public function testGetResponse(){
        $this->serverProperties->expects($this->once())->method('getURI')
            ->with()->will($this->returnValue("/Expenses/Website/connection/signIn"));
        $this->serverProperties->expects($this->once())->method('getDocumentRoot')
            ->with()->will($this->returnValue("C:/wamp64/www"));
        $this->serverProperties->expects($this->once())->method('getCurrentFolder')
            ->with()->will($this->returnValue("C:\wamp64\www\Expenses\Website"));
        $this->factories["connection"]->expects($this->once())->method('createRequest')
            ->with()->will($this->returnValue($this->request));
        $this->request->expects($this->once())->method('execute');
        $this->request->expects($this->once())->method('getResponse')
            ->with()->will($this->returnValue("Answer"));
        $this->router = new Router($this->serverProperties, $this->factories);
        $this->router->resolveRoute();
        $this->assertEquals("Answer", $this->router->getResponse());
    }
}
