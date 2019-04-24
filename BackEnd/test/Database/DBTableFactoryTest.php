<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 11:05 PM
 */
namespace BackEnd\Tests\Database;
use BackEnd\Database\DBTables;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\Database;
use BackEnd\Database\DBTableFactory;

class DBTableFactoryTest extends TestCase
{
    protected $driver;
    protected $database;
    private $classNames = ["DBCategories", "DBCurrencies", "DBExpenses", "DBPayees", "DBUsers",
        "DBSubCategories", "DBAccounts", "DBExpenseStates"];
    private $tables = [];
    private $factory;

    public function setUp(){
        $classIndex = 0;
        foreach($this->classNames as $className){
            $this->tables[$classIndex] = strtolower($className);
            $classIndex += 1;
        }
        $this->factory = new DBTableFactory();
    }

    public function testCreateTable(){
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()->setMethods(["getDriver", "getTableByName"])->getMock();
        $this->database->expects($this->exactly(10))
            ->method('getTableByName')
            ->withConsecutive([DBTables::USERS],
                [DBTables::CATEGORIES], [DBTables::SUBCATEGORIES], [DBTables::PAYEES], [DBTables::CURRENCIES], [DBTables::EXPENSES_STATES],
                [DBTables::USERS], [DBTables::CATEGORIES],
                [DBTables::USERS], [DBTables::CURRENCIES]);
        $tableIndex = 0;
        foreach($this->tables as $tableName){
            $table = $this->factory->createTable($tableName, $this->database);
            $expectedClass = explode('\\',$this->classNames[$tableIndex]);
            $expectedClass = end($expectedClass);
            $currentClass = explode('\\', get_class($table));
            $currentClass = end($currentClass);
            $this->assertEquals($expectedClass, $currentClass);
            $tableIndex += 1;
        }
    }

    public function testCreateTableWithWrongNameShouldThrow(){
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()->setMethods(["getDriver", "getTableByName"])->getMock();
        $this->expectException(\Exception::class);
        $this->factory->createTable("test", $this->database);
    }
}
