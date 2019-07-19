<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/17/2019
 * Time: 10:09 PM
 */

namespace BackEnd\Routing\Request\Currency;

use BackEnd\Database\DBCurrencies\DBCurrencies;
use PHPUnit\Framework\TestCase;

class RetrieveAllCurrenciesTest extends TestCase
{
    protected $currenciesTable;
    protected $data;
    protected $currency;
    public function setUp(){
        $this->currency = array('name' => 'Dollars', "short_name" => "USD", "currency_dollars_change" => 1);
        $this->currenciesTable = $this->getMockBuilder(DBCurrencies::class)->disableOriginalConstructor()
            ->setMethods(['getAllCurrencies'])->getMock();
    }

    public function test__construct(){
        $mandatoryFields = array();
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->currenciesTable, $request->getCurrenciesTable());
    }

    public function testExecute()
    {
        $request = $this->createRequest();
        $this->currenciesTable->expects($this->once())
            ->method('getAllCurrencies')
            ->with()->will($this->returnValue(array($this->currency)));
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

    protected function createRequest(){
        $request = new RetrieveAllCurrencies($this->currenciesTable, $this->data);
        return $request;
    }
}
