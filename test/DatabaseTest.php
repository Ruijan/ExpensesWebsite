<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:04 AM
 */
use PHPUnit\Framework\TestCase;
require_once(str_replace("test", "src", __DIR__."/").'Database.php');

class DatabaseTest extends TestCase
{

    private $dBHandler;
    private $driver;
    private $dbName;
    public function __construct(){
        parent::__construct();
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->dbName = "Expenses";
    }

    public function setUp(){
        $this->dBHandler = new \src\Database($this->driver, $this->dbName);
    }

    public function test__construct()
    {
        $this->assertSame($this->dBHandler->getDriver(), $this->driver);
        $this->assertTrue($this->driver->select_db($this->dbName));
    }

    public function testDropDatabase()
    {
        $this->dBHandler->dropDatabase();
        $this->assertFalse($this->driver->select_db($this->dbName));
    }
}
