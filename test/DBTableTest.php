<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 8:09 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBTable.php');
require_once(str_replace("test", "src", __DIR__."/").'Database.php');

use PHPUnit\Framework\TestCase;

class DBTableTest extends TestCase
{
    protected $table;
    protected $driver;
    protected $database;
    protected $name;
    protected $columns = ["ID" => "int(11)"];
    public function setUp(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->name = "test";
        $this->createTable();
    }

    public function createTable(){
        $this->table = new \src\DBTable($this->database, $this->name);
    }

    public function test__construct(){
        $this->assertTrue($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ") !== FALSE);
        $this->checkTableHeaders();
    }

    public function checkTableHeaders(){
        $columns = $this->driver->query("SHOW COLUMNS FROM ".$this->name);
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

    public function testCouldNotConstructTable(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->expectException(Exception::class);
        $this->table = new \src\DBTable($this->database, $this->name);
        $this->assertTrue($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 "));
    }

    public function testDropTable(){
        $this->table->dropTable();
        $this->assertFalse($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 "));
    }

    public function testDropTableTwiceShouldThrow(){
        $this->table->dropTable();
        $this->expectException(Exception::class);
        $this->table->dropTable();
        $this->assertFalse($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 "));
    }

    public function tearDown(){
        if($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ")) {
            $this->table->dropTable();
        }
    }
}
