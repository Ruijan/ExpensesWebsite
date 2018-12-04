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
    private $table;
    private $driver;
    private $database;
    private $name;
    public function setUp(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->name = "test";
        $this->table = new \src\DBTable($this->database, $this->name);
    }
    public function test__construct(){
        $this->assertTrue($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ") !== FALSE);
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
