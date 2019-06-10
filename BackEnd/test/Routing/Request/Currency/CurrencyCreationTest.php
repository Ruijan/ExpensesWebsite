<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 2:19 PM
 */

use BackEnd\Routing\Request\Currency\CurrencyCreation;
use PHPUnit\Framework\TestCase;

class CurrencyCreationTest extends TestCase
{
    private $currenciesTable;
    private $currency;
    public function setUp()
    {
        $this->currency = array("name" => "Current",
            "short_name" => 5,
            "currency_dollars_change" => 1.1234);
        $this->currenciesTable = $this->getMockBuilder(\BackEnd\Database\DBCurrencies::class)
            ->disableOriginalConstructor()
            ->setMethods(['addCurrency'])
            ->getMock();
    }

    public function test__construct()
    {
        $mandatoryFields = ["name", "short_name", "currency_dollars_change"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->currenciesTable, $request->getCurrenciesTable());
    }

    public function test__constructWithMissingParameters()
    {
        $this->currency = array();
        $request = $this->createRequest();
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }

    public function testGetResponse(){
        $request = $this->createRequest();
        $this->currenciesTable->expects($this->once())
            ->method('addCurrency')->with($this->currency["name"], $this->currency["short_name"]);
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    protected function createRequest()
    {
        $currencyCreationRequest = new CurrencyCreation($this->currenciesTable, $this->currency);
        return $currencyCreationRequest;
    }
}
