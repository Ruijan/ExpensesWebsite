<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/24/2019
 * Time: 8:59 PM
 */

namespace BackEnd\Tests\UseCases;

use PHPUnit\Framework\TestCase;
use BackEnd\Application;
use BackEnd\Database\Database;
use BackEnd\Routing\Request\RequestFactory;

abstract class UseCaseTest extends TestCase
{
    /** @var Database */
    protected $db;
    public function setUp()
    {
        $app = new Application();
        $this->db = $app->getDatabase();
    }
    abstract public function testPipelineExecution();

    protected function getResponseFromRequest($requestName, RequestFactory $requestFactory, $data){
        $request = $requestFactory->createRequest($requestName,$data);
        $request->execute();
        return json_decode($request->getResponse(), true);
    }

    protected function assertResponseStatus($answer){
        if($answer["STATUS"] != "OK"){
            $this->assertEquals("", $answer["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $answer["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $answer["STATUS"]);
        }
    }

    public function tearDown()
    {
        $this->db->dropDatabase();
    }
}
