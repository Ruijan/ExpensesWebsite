<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 4:59 PM
 */

namespace BackEnd\Tests\Database\DBExpenses;

use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBExpenses\DBExpenses;
use BackEnd\Expense;
use BackEnd\Database\DBExpenses\WrongTypeKeyException;
use BackEnd\Database\DBExpenses\InsertionKeyException;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Database\DBPayees;
use BackEnd\Database\DBCurrencies;
use BackEnd\Database\DBTables;

class DBExpensesTest extends TableCreationTest
{
    private $expense;
    private $categoriesTable;
    private $subCategoriesTable;
    private $payeesTable;
    private $currenciesTable;
    private $statesTable;
    private $expenseArray = array(
        "id" => 1,
        "account_id" => 12,
        "expense_date" => "2018-01-01 00:00:00",
        "location" => "Lausanne",
        "payee" => "Migros",
        "payee_id" => 1,
        "category" => "Food",
        "category_id" => 1,
        "sub_category" => "Groceries",
        "sub_category_id" => 1,
        "amount" => 60.52,
        "currency" => "CHF",
        "currency_id" => 1,
        "state" => "Paid",
        "state_id" => 2
    );

    public function setUp()
    {
        $this->initMocks();
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "LOCATION" => "char(50)",
            "ACCOUNT_ID" => "int(11)",
            "PAYEE_ID" => "int(11)",
            "CATEGORY_ID" => "int(11)",
            "SUB_CATEGORY_ID" => "int(11)",
            "EXPENSE_DATE" => "datetime",
            "AMOUNT" => "double",
            "CURRENCY_ID" => "int(11)",
            "STATE_ID" => "int(11)"];
        $this->name = "expenses";

    }

    protected function initMocks(): void
    {
        $this->expense = parent::getMockBuilder(Expense::class)->disableOriginalConstructor()->setMethods(['asArray'])->getMock();
        $this->categoriesTable = parent::getMockBuilder(DBCategories::class)->disableOriginalConstructor()->setMethods(['getCategoryFromID'])->getMock();
        $this->subCategoriesTable = parent::getMockBuilder(DBSubCategories::class)->disableOriginalConstructor()->setMethods(['getSubCategoryFromID'])->getMock();
        $this->payeesTable = parent::getMockBuilder(DBPayees::class)->disableOriginalConstructor()->setMethods(['getPayeeFromID'])->getMock();
        $this->currenciesTable = parent::getMockBuilder(DBCurrencies::class)->disableOriginalConstructor()->setMethods(['getCurrencyFromID'])->getMock();
        $this->statesTable = parent::getMockBuilder(DBStates::class)->disableOriginalConstructor()->setMethods(['getStateFromID'])->getMock();
    }

    public function createTable()
    {
        $this->database->addTable($this->categoriesTable, DBTables::CATEGORIES);
        $this->database->addTable($this->subCategoriesTable, DBTables::SUBCATEGORIES);
        $this->database->addTable($this->payeesTable, DBTables::PAYEES);
        $this->database->addTable($this->currenciesTable, DBTables::CURRENCIES);
        $this->database->addTable($this->statesTable, DBTables::EXPENSES_STATES);
        $this->table = new DBExpenses($this->database);
        $this->assertEquals($this->categoriesTable, $this->table->getCategoriesTable());
        $this->assertEquals($this->subCategoriesTable, $this->table->getSubCategoriesTable());
        $this->assertEquals($this->payeesTable, $this->table->getPayeesTable());
        $this->assertEquals($this->currenciesTable, $this->table->getCurrenciesTable());
        $this->assertEquals($this->statesTable, $this->table->getStatesTable());
    }

    public function initTable()
    {
        $this->table->init();
    }

    public function testAddExpenseWithWrongStateShouldThrow()
    {
        $wrongExpenseArray = $this->expenseArray;
        $wrongExpenseArray["state_id"] = "Paid";
        $this->expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($wrongExpenseArray));

        try {
            $this->table->addExpense($this->expense);
            $this->assertTrue(False);
        } catch (WrongTypeKeyException $e) {
            $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM ' . $this->name)->fetch_all();
            $this->assertEquals(0, $nbExpenses[0][0]);
        }
    }

    public function testAddExpense()
    {
        $this->expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($this->expenseArray));
        $this->table->addExpense($this->expense);
        $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM ' . $this->name)->fetch_all()[0][0];
        $this->assertEquals(1, $nbExpenses);
    }

    public function testAddExpenseWithNoID()
    {
        $noIDExpense = [];
        foreach ($this->expenseArray as $key => $value) {
            $noIDExpense[$key] = NULL;
            if (strpos($key, 'id') === false) {
                $noIDExpense[$key] = $value;
            }
        }

        $this->expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($noIDExpense));
        $this->table->addExpense($this->expense);
        $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM ' . $this->name)->fetch_all()[0][0];
        $this->assertEquals(1, $nbExpenses);
    }

    public function testAddExpenseWithNoLocation()
    {
        $noLocationExpense = $this->expenseArray;
        $noLocationExpense["location"] = NULL;
        $this->expense->expects($this->once())
            ->method('asArray')
            ->with()->will($this->returnValue($noLocationExpense));
        try {
            $this->table->addExpense($this->expense);
            $this->assertTrue(False);
        } catch (InsertionKeyException $e) {
            $nbExpenses = $this->driver->query('SELECT COUNT(*) FROM ' . $this->name)->fetch_all()[0][0];
            $this->assertEquals(0, $nbExpenses);
        }
    }

    public function testGetExpensesForAccountID()
    {
        $this->expense->expects($this->exactly(2))
            ->method('asArray')
            ->with()->will($this->returnValue($this->expenseArray));
        $this->categoriesTable->expects($this->exactly(2))
            ->method('getCategoryFromID')
            ->with($this->expenseArray["category_id"])->will($this->returnValue("Food"));
        $this->subCategoriesTable->expects($this->exactly(2))
            ->method('getSubCategoryFromID')
            ->with($this->expenseArray["sub_category_id"])->will($this->returnValue("Groceries"));
        $this->payeesTable->expects($this->exactly(2))
            ->method('getPayeeFromID')
            ->with($this->expenseArray["payee_id"])->will($this->returnValue("Migros"));
        $this->currenciesTable->expects($this->exactly(2))
            ->method('getCurrencyFromID')
            ->with($this->expenseArray["currency_id"])->will($this->returnValue("CHF"));
        $this->statesTable->expects($this->exactly(2))
            ->method('getStateFromID')
            ->with($this->expenseArray["state_id"])->will($this->returnValue("Paid"));
        $this->table->addExpense($this->expense);
        $this->table->addExpense($this->expense);
        $expenses = $this->table->getExpensesForAccountID($this->expenseArray["account_id"]);
        $this->assertEquals(2, count($expenses));
        $count = 1;
        foreach ($expenses as $expense) {
            $this->expenseArray["id"] = $count;
            $this->assertEquals($this->expenseArray, $expense->asArray());
            $count += 1;
        }
    }
}
