<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:04 AM
 */

namespace BackEnd\Tests\Database;

use BackEnd\Database\DBTable;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;

class DatabaseTest extends TestCase
{

    private $dBHandler;
    private $driver;
    private $dbName;

    public function __construct()
    {
        parent::__construct();
        $this->driver = new \mysqli("127.0.0.1", "root", "");
        $this->dbName = "Expenses";
    }

    public function setUp()
    {
        $this->dBHandler = new Database($this->driver, $this->dbName);
    }

    public function test__construct()
    {
        $this->assertSame($this->dBHandler->getDriver(), $this->driver);
        $this->assertTrue($this->driver->select_db($this->dbName));
    }

    public function test__constructWithDriverFailureShouldThrow()
    {
        $observer = $this->getMockBuilder(\mysqli::class)->setMethods(['query', 'real_escape_string'])->getMock();
        $observer->expects($this->once())
            ->method('query')->will($this->returnValue(false));
        $observer->expects($this->once())
            ->method('real_escape_string')->will($this->returnValue(false));
        $this->expectException(\Exception::class);
        $this->dBHandler = new Database($observer, $this->dbName);
    }

    public function testInit(){
        $table = $this->getMockBuilder(DBTable::class)->disableOriginalConstructor()->setMethods(['init'])->getMock();
        $this->dBHandler->addTable($table, DBTables::USERS);
        $table->expects($this->once())->method('init');
        $this->dBHandler->init();
    }

    public function testDropDatabase()
    {
        $this->dBHandler->dropDatabase();
        $this->assertFalse($this->driver->select_db($this->dbName));
    }

    public function testDropDatabaseTwiceShouldThrow()
    {
        $this->expectException(\Exception::class);
        $this->dBHandler->dropDatabase();
        $this->dBHandler->dropDatabase();

    }

    public function testExist()
    {
        $this->assertTrue($this->dBHandler->exist());
    }

    public function testGetDBName()
    {
        $this->assertEquals($this->dBHandler->getDBName(), $this->dbName);
    }

    public function testAddTable()
    {
        $tableMock = $this->getMockBuilder(DBTable::class)->disableOriginalConstructor()->getMock();
        $this->dBHandler->addTable($tableMock, "test");
        $this->assertEquals($this->dBHandler->getTableByName("test"), $tableMock);
    }

    public function test__destruct()
    {
        $observer = $this->getMockBuilder(\mysqli::class)->setMethods(['close', 'select_db', 'query', 'real_escape_string'])->getMock();
        $observer->expects($this->once())
            ->method('query')->will($this->returnValue(true));
        $observer->expects($this->once())
            ->method('real_escape_string')->will($this->returnValue(true));
        $observer->expects($this->once())
            ->method('select_db');
        $observer->expects($this->once())
            ->method('close');
        $this->dBHandler = new Database($observer, $this->dbName);
        unset($this->dBHandler);
    }

    public function tearDown()
    {
        unset($this->dBHandler);
    }

    public function __destruct()
    {
        unset($this->driver);
    }
}
