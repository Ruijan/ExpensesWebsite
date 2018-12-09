<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 11:05 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBTableFactory.php');
require_once(str_replace("test", "src", __DIR__."/").'Database.php');
use PHPUnit\Framework\TestCase;

class DBTableFactoryTest extends TestCase
{
    protected $driver;
    protected $database;
    private $tables = ["DBCategories", "DBCurrency", "DBExpenses", "DBPayee", "DBPayer", "DBSubCategories"];
    private $dbTables = ["categories", "sub_categories", "currencies", "expenses", "payees", "payers"];

    public function testCreateTable(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        foreach($this->tables as $tableName){
            $table = \src\DBTableFactory::createTable($tableName, $this->database);
            $this->assertEquals(get_class($table),'src\\'.$tableName);
            $table->dropTable();
        }
    }

    public function testCreateTableWithWrongNameShouldThrow(){
        $this->driver = new mysqli("127.0.0.1", "root", "");
        $this->database = new \src\Database($this->driver, "expenses");
        $this->expectException(Exception::class);
        \src\DBTableFactory::createTable("test", $this->database);
    }

    public function tearDown()
    {
        foreach($this->dbTables as $tableName){
            $query = "DROP TABLE ".$tableName;
            $this->driver->query($query);
        }
    }

    public function __destruct()
    {
        if($this->driver->connect_errno === FALSE) {
            foreach ($this->dbTables as $tableName) {
                $query = "DROP TABLE " . $tableName;
                $this->driver->query($query);
            }
        }
    }
}
