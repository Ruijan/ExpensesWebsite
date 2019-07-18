<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/18/2019
 * Time: 9:35 PM
 */
namespace BackEnd\Tests\Routing\Request;
use PHPUnit\Framework\TestCase;

abstract class RequestTest extends TestCase
{
    protected $data;

    /**
     * @return \BackEnd\Routing\Request\Request
     */
    abstract protected function createRequest();

    public function testInitializationWithMissingParameters()
    {
        $this->data = array();
        $this->createRequest();
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($this->request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }
}
