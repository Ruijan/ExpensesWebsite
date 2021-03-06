<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:08 PM
 */

namespace BackEnd\Tests\Database;

use BackEnd\Database\DBCurrencies\DBCurrencies;
use BackEnd\Database\DBCurrencies\UndefinedCurrencyException;
use BackEnd\Database\InsertionException;

class DBCurrenciesTest extends TableCreationTest
{
    protected $currencyName;
    protected $shortCurrencyName;

    public function setUp()
    {
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "SHORT_NAME" => "char(5)",
            "CURRENT_DOLLARS_CHANGE" => "int(11)"];
        $this->name = "currencies";
        $this->currencyName = "Swiss Francs";
        $this->shortCurrencyName = "CHF";
    }

    public function createTable()
    {
        $this->table = new DBCurrencies($this->database);
    }

    public function initTable()
    {
        $this->table->init();
    }

    public function testAddCurrency()
    {
        $this->table->addCurrency($this->currencyName, $this->shortCurrencyName);
        $result = $this->driver->query("SELECT * FROM " . $this->name)->fetch_assoc();
        $this->assertEquals($this->currencyName, $result["NAME"]);
        $this->assertEquals($this->shortCurrencyName, $result["SHORT_NAME"]);
    }

    public function testAddCurrencyTwiceShouldThrow()
    {
        $count = 0;
        $this->table->addCurrency($this->currencyName, $this->shortCurrencyName);
        try {
            $this->table->addCurrency($this->currencyName, $this->shortCurrencyName);
        } catch (InsertionException $e) {
            $result = $this->driver->query("SELECT * FROM " . $this->name);
            while ($row = $result->fetch_assoc()) {
                $this->assertEquals($this->currencyName, $row["NAME"]);
                $this->assertEquals($this->shortCurrencyName, $row["SHORT_NAME"]);
                $count += 1;
            }
            $this->assertEquals(1, $count);
            return;
        }
        $this->assertEquals(1, $count);
    }

    public function testGetCurrencyFromID()
    {
        $this->table->addCurrency($this->currencyName, $this->shortCurrencyName);
        $currency = $this->table->getCurrencyFromID(1);
        $this->assertEquals($this->currencyName, $currency["NAME"]);
    }

    public function testDeleteCurrency(){
        $this->table->addCurrency($this->currencyName, $this->shortCurrencyName);
        $this->table->deleteCurrency($this->currencyName, $this->shortCurrencyName);
        $doesCurrencyExist = $this->table->doesCurrencyExist($this->currencyName, $this->shortCurrencyName);
        $this->assertEquals(false, $doesCurrencyExist);
    }

    public function testDeleteWrongCurrency(){
        $this->expectException(UndefinedCurrencyException::class);

        $this->table->deleteCurrency($this->currencyName, $this->shortCurrencyName);
    }

    public function testGetAllStates(){
        $this->table->addCurrency($this->currencyName, $this->shortCurrencyName);
        $this->table->addCurrency("Euros", "EUR");
        $currencies = $this->table->getAllCurrencies();
        $this->assertEquals(2, sizeof($currencies));
    }
}
