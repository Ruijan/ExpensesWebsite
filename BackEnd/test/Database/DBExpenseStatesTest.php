<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 6:32 PM
 */

namespace BackEnd\Tests\Database;

use Backend\Database\DBExpenseStates\InsertionException;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBExpenseStates\DBExpenseStates;

class DBExpenseStatesTest extends TableCreationTest
{
    private $stateName = "PAID";

    public function setUp()
    {
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)"];
        $this->name = "expense_states";
    }

    public function createTable()
    {
        $this->table = new DBExpenseStates($this->database);
    }

    public function initTable()
    {
        $this->table->init();
    }

    public function testAddState()
    {
        $this->table->addState($this->stateName);
        $result = $this->driver->query("SELECT * FROM " . $this->name)->fetch_assoc();
        $this->assertEquals($this->stateName, $result["NAME"]);
    }

    public function testAddExistingStateShouldThrow()
    {
        $this->table->addState($this->stateName);
        $this->expectException(InsertionException::class);
        $this->table->addState($this->stateName);
    }

    public function testGetExpenseStateFromID(){
        $this->table->addState($this->stateName);
        $state = $this->table->getExpenseStateFromID(1);
        $this->assertEquals($this->stateName, $state["NAME"]);
    }
}