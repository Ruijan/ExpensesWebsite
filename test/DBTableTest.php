<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 8:09 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBTable.php');

require_once("TableCreationTest.php");

class DBTableTest extends TableCreationTest
{
    public function setUp()
    {
        $this->name = "test";
        $this->columns = ["ID" => "int(11)"];
        parent::setUp();
    }

    public function createTable(){
        $this->table = new \src\DBTable($this->database, $this->name);
    }

    public function testCouldNotConstructTableTwice(){
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
}
