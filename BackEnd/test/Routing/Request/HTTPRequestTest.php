<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/12/2019
 * Time: 11:14 PM
 */

namespace BackEnd\Tests\Routing\Request;

use BackEnd\Routing\Request\HTTPRequest;
use PHPUnit\Framework\TestCase;

class HTTPRequestTest extends TestCase
{
    public function testCamel(){
        $request = $this->getMockBuilder('BackEnd\Routing\Request\HTTPRequest')
            ->getMockForAbstractClass();
        $this->assertEquals("test", $request->toCamelCase("TEST"));
        $this->assertEquals("testUnderscore", $request->toCamelCase("TEST_UNDERSCORE"));
    }
}
