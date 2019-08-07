<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/24/2019
 * Time: 8:08 PM
 */

use PHPUnit\Framework\TestCase;
use BackEnd\Database\Database;
use \BackEnd\Routing\Request\Expense\ExpenseCreation;
use BackEnd\Database\DBTables;
use \BackEnd\Routing\Request\Expense\ExpenseRequestFactory;
use BackEnd\Routing\Request\Expense\DeleteExpense;
use \BackEnd\Routing\Request\Expense\RetrieveAllExpenses;

class ExpenseRequestFactoryTest extends TestCase
{
    private $database;

    public function setUp()
    {
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()
            ->setMethods(["getDriver", "getTableByName"])->getMock();
    }

    public function test__construct()
    {
        $factory = new ExpenseRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateExpenseCreationRequest()
    {
        $factory = $this->createSuccessfulFactory();
        $request = $factory->createRequest("Create", array());
        $this->assertEquals(ExpenseCreation::class, get_class($request));
    }

    public function testCreateRetrieveAllExpensesFromAccountRequest()
    {
        $factory = $this->createSuccessfulFactory();
        $request = $factory->createRequest("RetrieveAllFromAccount", array());
        $this->assertEquals(RetrieveAllExpenses::class, get_class($request));
    }

    public function testCreateDeleteExpenseRequest()
    {
        $factory = $this->createSuccessfulFactory();
        $request = $factory->createRequest("Delete", array());
        $this->assertEquals(DeleteExpense::class, get_class($request));
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new ExpenseRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut", array());
    }

    /**
     * @return ExpenseRequestFactory
     */
    protected function createSuccessfulFactory(): ExpenseRequestFactory
    {
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::EXPENSES], [DBTables::USERS]);
        $factory = new ExpenseRequestFactory($this->database);
        return $factory;
    }
}
