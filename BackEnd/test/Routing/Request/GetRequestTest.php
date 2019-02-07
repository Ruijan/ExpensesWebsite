<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/8/2019
 * Time: 12:50 AM
 */

namespace BackEnd\Tests\Request;

use BackEnd\Routing\Request\GetRequest;
use PHPUnit\Framework\TestCase;

class GetRequestTest extends TestCase
{
    public function setUp()
    {
        $_GET = array("EMAIL" => "dwaddkjaw@eofjea.com");
    }

    public function test__construct(){
        $request = $this->getMockBuilder('BackEnd\Routing\Request\GetRequest')
            ->getMockForAbstractClass();
        $request->init();
        $this->assertEquals($_GET["EMAIL"], $request->email);
    }
}
