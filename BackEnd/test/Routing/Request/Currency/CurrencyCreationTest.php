<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 2:19 PM
 */

use BackEnd\Routing\Request\Currency\CurrencyCreation;
use \BackEnd\Database\DBCurrencies\DBCurrencies;
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class CurrencyCreationTest extends ConnectedRequestTest
{
    private $currenciesTable;
    public function setUp()
    {
        $this->data = array("name" => "Current",
            "short_name" => 5,
            "currency_dollars_change" => 1.1234);
        parent::setUp();
        $this->currenciesTable = $this->getMockBuilder(DBCurrencies::class)
            ->disableOriginalConstructor()
            ->setMethods(['addCurrency'])
            ->getMock();
    }

    public function test__construct()
    {
        $this->mandatoryFields[] = "name";
        $this->mandatoryFields[] = "short_name";
        $this->mandatoryFields[] = "currency_dollars_change";
        parent::test__construct();
        $this->assertEquals($this->currenciesTable, $this->request->getCurrenciesTable());
    }

    public function testGetResponse(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->currenciesTable->expects($this->once())
            ->method('addCurrency')->with($this->data["name"], $this->data["short_name"]);
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true );
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    public function testExecuteFails(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $exception = new \BackEnd\Database\InsertionException("", ["name"], "plop", "plop");
        $this->currenciesTable->expects($this->once())
            ->method('addCurrency')->with($this->data["name"], $this->data["short_name"])
            ->will($this->throwException($exception));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }

    protected function createRequest()
    {
        $this->request = new CurrencyCreation($this->currenciesTable, $this->usersTable, $this->user, $this->data);
    }
}
