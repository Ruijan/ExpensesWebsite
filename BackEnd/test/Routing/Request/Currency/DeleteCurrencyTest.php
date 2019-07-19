<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/11/2019
 * Time: 9:05 PM
 */

namespace BackEnd\Tests\Request;

use BackEnd\Database\DBCurrencies\DBCurrencies;
use BackEnd\Routing\Request\Currency\DeleteCurrency;
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class DeleteCurrencyTest extends ConnectedRequestTest
{
    /** @var DBCurrencies */
    private $currencyTable;
    public function setUp(){
        $this->data = array("name" => "Euros",
            "short_name" => "EUR");
        parent::setUp();

        $this->mandatoryFields[] = "name";
        $this->mandatoryFields[] = "short_name";
        $this->currencyTable = $this->getMockBuilder(DBCurrencies::class)->disableOriginalConstructor()
            ->setMethods(['deleteCurrency', "doesCurrencyExist"])->getMock();
    }

    public function test__construct(){

        parent::test__construct();
        $this->assertEquals($this->currencyTable, $this->request->getCurrencyTable());
    }

    public function testExecute(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->currencyTable->expects($this->once())
            ->method('deleteCurrency')
            ->with($this->data["name"], $this->data["short_name"]);
        $this->currencyTable->expects($this->once())
            ->method('doesCurrencyExist')
            ->with()->will($this->returnValue(true));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    public function testExecuteWithInvalidCurrency(){
        $this->createRequest();
        $this->currencyTable->expects($this->once())
            ->method('doesCurrencyExist')
            ->with()
            ->will($this->returnValue(false));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("currency with Name", $response["ERROR_MESSAGE"]);
    }

    protected function createRequest(){
        $this->request = new DeleteCurrency($this->currencyTable, $this->usersTable, $this->user, $this->data);
    }
}
