<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/14/2019
 * Time: 8:58 PM
 */

use BackEnd\Routing\Request\ExpenseState\ExpenseStateCreation;
use PHPUnit\Framework\TestCase;

class ExpenseStateCreationTest extends \BackEnd\Tests\Routing\Request\ConnectedRequestTest
{
    private $expenseStatesTable;

    public function setUp()
    {
        $this->data = array("name" => "Paid");
        parent::setUp();
        $this->mandatoryFields[] = "name";
        $this->expenseStatesTable = $this->getMockBuilder(\BackEnd\Database\DBExpenseStates\DBExpenseStates::class)->disableOriginalConstructor()
            ->setMethods(['addState'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->expenseStatesTable, $this->request->getExpenseStatesTable());
    }

    public function testExecute()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->expenseStatesTable->expects($this->once())
            ->method('addState');
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    public function testExecuteFails(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $exception = new \BackEnd\Database\InsertionException("", ["name"], "plop", "plop");
        $this->expenseStatesTable->expects($this->once())
            ->method('addState')
            ->will($this->throwException($exception));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }

    protected function createRequest()
    {
        $this->request = new ExpenseStateCreation($this->expenseStatesTable, $this->usersTable, $this->user, $this->data);
    }
}
