<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:50 PM
 */
require_once(str_replace("test", "src", __DIR__."/").'Database.php');
use PHPUnit\Framework\TestCase;

abstract class TableCreationTest extends TestCase
{
    protected $table;
    protected $driver;
    protected $database;
    protected $name;
    protected $columns;

    public function setUp(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->createTable();
    }

    public function test__construct(){
        $this->assertTrue($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ") !== FALSE);
        $this->checkTableHeaders();
    }

    abstract public function createTable();

    public function checkTableHeaders(){
        $columns = $this->driver->query("SHOW COLUMNS FROM ".$this->name);
        $existingColumn = [];
        $keys = array_keys($this->columns);
        foreach($keys as $column) {
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
        if($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ")) {
            $this->table->dropTable();
        }
    }
}
