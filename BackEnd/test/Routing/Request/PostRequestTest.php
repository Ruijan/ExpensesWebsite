<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 8:59 PM
 */

namespace BackEnd\Tests\Request;

use BackEnd\Routing\Request\PostRequest;
use PHPUnit\Framework\TestCase;

class PostRequestTest extends TestCase
{
    public function setUp()
    {
        $_POST = array("EMAIL" => "dwaddkjaw@eofjea.com");
    }

    public function test__construct(){
        $request = $this->getMockBuilder('BackEnd\Routing\Request\PostRequest')
            ->getMockForAbstractClass();
        $request->init();
        $this->assertEquals($_POST["EMAIL"], $request->email);
    }
}
