<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:35 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBCategories.php');
use PHPUnit\Framework\TestCase;

class DBCategoryTest extends TestCase
{
    private $categories;
    private $driver;
    private $database;

    public function setUp(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->categories = new \src\DBCategories($this->database);
    }

    public function test__construct(){
        $this->assertTrue($this->driver->query("SELECT 1 FROM categories LIMIT 1 ") !== FALSE);
    }

    public function testCouldNotConstructTable(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->expectException(Exception::class);
        $this->categories = new \src\DBCategories($this->database);
        $this->assertTrue($this->driver->query("SELECT 1 FROM categories LIMIT 1 "));
    }

    public function testDropTable(){
        $this->categories->dropTable();
        $this->assertFalse($this->driver->query("SELECT 1 FROM categories LIMIT 1 "));
    }

    public function tearDown(){
        if($this->driver->query("SELECT 1 FROM categories LIMIT 1 ")) {
            $this->categories->dropTable();
        }
    }
}
