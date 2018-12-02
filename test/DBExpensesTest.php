<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 4:59 PM
 */

use PHPUnit\Framework\TestCase;
require_once(str_replace("test", "src", __DIR__."/").'DBExpenses.php');

class DBExpensesTest extends TestCase
{
    private $expenses;
    private $driver;
    private $database;

    public function setUp(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->expenses = new \src\DBExpenses($this->database);
    }
    public function test__construct(){

        $this->assertTrue($this->driver->query("SELECT 1 FROM expenses LIMIT 1 ") !== FALSE);
    }

    public function testCouldNotConstructTable(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->expectException(Exception::class);
        $this->expenses = new \src\DBExpenses($this->database);
        $this->assertTrue($this->driver->query("SELECT 1 FROM expenses LIMIT 1 "));
    }

    public function testDropTable(){
        $this->expenses->dropTable();
        $this->assertFalse($this->driver->query("SELECT 1 FROM expenses LIMIT 1 "));
    }

    public function tearDown(){
        $this->expenses->dropTable();
    }

}
