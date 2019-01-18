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
    private $tables = ["DBCategories", "DBCurrency", "DBExpenses", "DBPayee", "DBPayer", "DBSubCategories", "DBAccount"];
    private $dbTables = ["categories", "sub_categories", "currencies", "expenses", "payees", "payers", "accounts"];
    private $factory;

    public function setUp(){
        $this->factory = new \src\DBTableFactory();
    }

    public function testCreateTable(){
        $this->database = $this->getMockBuilder(\src\Database::class)->disableOriginalConstructor()->setMethods(["getDriver", "getTableByName"])->getMock();
        $this->database->expects($this->exactly(4))
            ->method('getTableByName')
            ->withConsecutive(["dbpayer"], ["dbpayer"], ["dbcategories"], ["dbpayer"]);
        /*$this->database->expects($this->once())
            ->method('getTableByName')
            ->with("dbcategories");*/
        foreach($this->tables as $tableName){
            $table = $this->factory->createTable($tableName, $this->database);
            $this->assertEquals(get_class($table),'src\\'.$tableName);
        }
    }

    public function testCreateTableWithWrongNameShouldThrow(){
        $this->database = $this->getMockBuilder(\src\Database::class)->disableOriginalConstructor()->setMethods(["getDriver", "getTableByName"])->getMock();
        $this->expectException(Exception::class);
        $this->factory->createTable("test", $this->database);
    }
}
