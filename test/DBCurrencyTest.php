<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:08 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBCurrency.php');
require_once("DBTableTest.php");

class DBCurrencyTest extends DBTableTest
{
    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "CURRENT_DOLLARS_CHANGE" => "int(11)"];
        $this->name = "currencies";
    }

    public function createTable()
    {
        $this->table = new \src\DBCurrency($this->database);
    }
}
