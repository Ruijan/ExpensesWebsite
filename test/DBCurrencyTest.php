<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:08 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBCurrency.php');
use PHPUnit\Framework\TestCase;

class DBCurrencyTest extends TestCase
{
    private $currency;
    private $driver;
    private $database;
    private $columns = ["ID" => "int(11)",
        "NAME" => "char(50)",
        "CURRENT_DOLLARS_CHANGE" => "int(11)"];

    public function setUp(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->currency = new \src\DBCurrency($this->database);
    }
    public function test__construct(){
        $this->assertTrue($this->driver->query("SELECT 1 FROM currencies LIMIT 1 ") !== FALSE);
        $columns = $this->driver->query("SHOW COLUMNS FROM currencies");
        $existingColumn = [];
        foreach($this->columns as $column => $value) {
            $existingColumn[$column] = 0;
        }
        $this->assertEquals($columns->num_rows, count($this->columns));
        foreach($columns as $column){
            $this->assertEquals($column["Type"], $this->columns[$column["Field"]]);
            $existingColumn[$column["Field"]] += 1;
            $this->assertEquals($existingColumn[$column["Field"]], 1);
        }
    }

    public function tearDown(){
        if($this->driver->query("SELECT 1 FROM currencies LIMIT 1 ")) {
            $this->currency->dropTable();
        }
    }
}
