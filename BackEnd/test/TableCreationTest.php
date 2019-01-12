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
        $this->initTable();
    }

    public function test__construct(){
        $this->assertEquals($this->name, $this->table->getName());
        $this->assertEquals($this->driver, $this->table->getDriver());
        $this->assertEquals($this->database, $this->table->getDatabase());
    }

    public function testInit(){
        $this->assertTrue($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ") !== FALSE);
        $this->checkTableHeaders();
    }

    abstract public function createTable();
    abstract public function initTable();

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

    public function testCouldNotInitTableTwice(){

        try{
            $this->initTable();
        }
        catch(Exception $e){
            $query = $this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ");
            $this->assertTrue($query !== FALSE);
            return;
        }
        $this->assertTrue(false);
    }

    public function tearDown(){
        if($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ")) {
            $this->table->dropTable();
        }
    }
}
