<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 11:05 PM
 */
namespace BackEnd\Tests\Database;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\Database;
use BackEnd\Database\DBTableFactory;

class DBTableFactoryTest extends TestCase
{
    protected $driver;
    protected $database;
    private $tables = ["DBCategories", "DBCurrencies", "DBExpenses", "DBPayees", "DBUsers",
        "DBSubCategories", "DBAccounts", "DBExpenseStates"];
    private $tableNameSpace = ["", "", "", "", "", "", "DBAccounts"];
    private $factory;

    public function setUp(){
        $this->factory = new DBTableFactory();
    }

    public function testCreateTable(){
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()->setMethods(["getDriver", "getTableByName"])->getMock();
        $this->database->expects($this->exactly(10))
            ->method('getTableByName')
            ->withConsecutive(["dbuser"],
                ["dbcategories"], ["dbsubcategories"], ["dbpayees"], ["dbcurrencies"], ["dbstates"],
                ["dbuser"], ["dbcategories"],
                ["dbuser"], ["dbcurrencies"]);
        foreach($this->tables as $tableName){
            $table = $this->factory->createTable($tableName, $this->database);
            $expectedClass = explode('\\',$tableName);
            $expectedClass = end($expectedClass);
            $currentClass = explode('\\', get_class($table));
            $currentClass = end($currentClass);
            $this->assertEquals($expectedClass, $currentClass);
        }
    }

    public function testCreateTableWithWrongNameShouldThrow(){
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()->setMethods(["getDriver", "getTableByName"])->getMock();
        $this->expectException(\Exception::class);
        $this->factory->createTable("test", $this->database);
    }
}
