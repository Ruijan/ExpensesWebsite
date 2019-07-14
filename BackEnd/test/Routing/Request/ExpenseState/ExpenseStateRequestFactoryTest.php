<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 9:31 PM
 */

use BackEnd\Routing\Request\ExpenseState\ExpenseStateRequestFactory;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\Database;
use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\ExpenseState\ExpenseStateCreation;
use BackEnd\Routing\Request\ExpenseState\DeleteExpenseState;
use \BackEnd\Routing\Request\ExpenseState\RetrieveAllExpenseStates;

class ExpenseStateRequestFactoryTest extends TestCase
{
    private $database;

    public function setUp()
    {
        $this->database = $this->getMockBuilder(Database::class)->disableOriginalConstructor()
            ->setMethods(["getDriver", "getTableByName"])->getMock();    }

    public function testCreateExpenseStateCreationRequest()
    {
        $factory = $this->createSuccessfulFactory();
        $request = $factory->createRequest("Create");
        $this->assertEquals(ExpenseStateCreation::class, get_class($request));
    }

    public function testCreateRetrieveAllExpenseStatesRequest()
    {
        $factory = $this->createSuccessfulFactory();
        $request = $factory->createRequest("RetrieveAll");
        $this->assertEquals(RetrieveAllExpenseStates::class, get_class($request));
    }

    public function testCreateDeleteExpenseStateRequest()
    {
        $this->database->expects($this->exactly(1))
            ->method('getTableByName')
            ->with(DBTables::EXPENSES_STATES);
        $factory = new ExpenseStateRequestFactory($this->database);
        $request = $factory->createRequest("Delete");
        $this->assertEquals(DeleteExpenseState::class, get_class($request));
    }

    public function test__construct()
    {
        $factory = new ExpenseStateRequestFactory($this->database);
        $this->assertEquals($this->database, $factory->getDatabase());
    }

    public function testCreateWrongTypeOfRequestShouldThrow(){
        $factory = new ExpenseStateRequestFactory($this->database);
        $this->expectException(\InvalidArgumentException::class);
        $request = $factory->createRequest("Tutut");
    }

    /**
     * @return ExpenseStateRequestFactory
     */
    protected function createSuccessfulFactory(): ExpenseStateRequestFactory
    {
        $this->database->expects($this->exactly(2))
            ->method('getTableByName')
            ->withConsecutive([DBTables::EXPENSES_STATES], [DBTables::USERS]);
        $factory = new ExpenseStateRequestFactory($this->database);
        return $factory;
    }
}
