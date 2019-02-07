<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 10:24 PM
 */

namespace BackEnd\Tests\Request;

use PHPUnit\Framework\TestCase;
use BackEnd\Routing\ServerProperties;

class ServerPropertiesTest extends TestCase
{

    public function setUp()
    {
        $_SERVER["REQUEST_URI"] = "Website/BackEnd/";
    }

    public function test__construct(){
        $request = new ServerProperties();
        $this->assertEquals($_SERVER["REQUEST_URI"], $request->getURI());
    }
}
