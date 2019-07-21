<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 12:18 PM
 */

use BackEnd\Routing\Request\Payee\RetrieveAllPayees;
use PHPUnit\Framework\TestCase;

class RetrieveAllPayeesTest extends \BackEnd\Tests\Routing\Request\ConnectedRequestTest
{
    protected $payeesTable;

    public function setUp()
    {
        parent::setUp();
        $this->payeesTable = $this->getMockBuilder(\BackEnd\Database\DBPayees\DBPayees::class)->disableOriginalConstructor()
            ->setMethods(['getAllPayees'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->payeesTable, $this->request->getPayeesTable());
    }

    public function testExecute()
    {
        $payee = array(
            "name" => "Migros",
            "user_id" => 2,
            "added_date" => "2019-06-12 00:00:00"
        );
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->payeesTable->expects($this->once())
            ->method('getAllPayees')
            ->with()->will($this->returnValue(array($payee)));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        if ($response["STATUS"] == "ERROR") {
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        } else {
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    protected function createRequest()
    {
        $this->request = new RetrieveAllPayees($this->payeesTable, $this->usersTable, $this->user, $this->data);
    }
}
