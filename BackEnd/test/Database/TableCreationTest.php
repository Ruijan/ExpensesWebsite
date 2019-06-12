<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:50 PM
 */
namespace BackEnd\Tests\Database;
use \BackEnd\Database\Database;
use BackEnd\Database\TableCreationException;
use PHPUnit\Framework\TestCase;

abstract class TableCreationTest extends TestCase
{
    protected $table;
    protected $driver;
    protected $mockDriver;
    protected $database;
    protected $mockDatabase;
    protected $name;
    protected $columns;



    public function setUp(){
        $this->driver = new \mysqli("127.0.0.1", "root", "");
        $this->database = new Database($this->driver, "expenses");
        $this->mockDriver = $this->getMockBuilder(\mysqli::class)->disableOriginalConstructor()
            ->setMethods(['query', 'real_escape_string', 'fetch_assoc'])->getMock();
        $this->mockDatabase = $this->getMockBuilder(\BackEnd\Database::class)->disableOriginalConstructor()
            ->setMethods(['getDriver', 'addTable', 'getTableByName', 'getDBName', 'dropDatabase'])->getMock();
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

    public function testInitWithMissingColumn(){
        $this->checkTableHeaders();
        $this->driver->query("ALTER TABLE ".$this->name." DROP COLUMN EMAIL");
        $this->initTable();
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
        $this->assertEquals(count($this->columns), $columns->num_rows);
        foreach($columns as $column){
            $this->assertEquals($this->columns[$column["Field"]], $column["Type"]);
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
