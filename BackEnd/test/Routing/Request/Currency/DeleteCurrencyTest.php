<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/11/2019
 * Time: 9:05 PM
 */

namespace BackEnd\Tests\Request;

use BackEnd\Database\DBCurrencies;
use BackEnd\Routing\Request\Currency\DeleteCurrency;
use PHPUnit\Framework\TestCase;

class DeleteCurrencyTest extends TestCase
{
    /** @var DBCurrencies\DBCurrencies */
    private $currencyTable;
    private $currency;
    public function setUp(){
        $this->currency = array("name" => "Euros",
            "short_name" => "EUR");
        $this->currencyTable = $this->getMockBuilder(DBCurrencies::class)->disableOriginalConstructor()
            ->setMethods(['deleteCurrency', "doesCurrencyExist"])->getMock();
    }

    public function test__construct(){
        $mandatoryFields = ["name", "short_name"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->currencyTable, $request->getCurrencyTable());
    }

    public function testGetResponse(){
        $request = $this->createRequest();
        $this->currencyTable->expects($this->once())
            ->method('deleteCurrency')
            ->with($this->currency["name"], $this->currency["short_name"]);
        $this->currencyTable->expects($this->once())
            ->method('doesCurrencyExist')
            ->with()->will($this->returnValue(true));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    public function testGetResponseWithInvalidCurrency(){
        $request = $this->createRequest();
        $this->currencyTable->expects($this->once())
            ->method('doesCurrencyExist')
            ->with()
            ->will($this->returnValue(false));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("currency with Name", $response["ERROR_MESSAGE"]);
    }

    protected function createRequest(){
        $deleteAccountRequest = new DeleteCurrency($this->currencyTable, $this->currency);
        return $deleteAccountRequest;
    }
}
