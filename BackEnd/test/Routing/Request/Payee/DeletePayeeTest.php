<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:37 AM
 */

use BackEnd\Routing\Request\Payee\DeletePayee;
use BackEnd\Database\DBPayees\DBPayees;
use BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class DeletePayeeTest extends ConnectedRequestTest
{
    /** @var DBPayees */
    private $payeesTable;
    public function setUp(){
        $this->data = array("payee_id" => 1);
        parent::setUp();

        $this->mandatoryFields[] = "payee_id";
        $this->payeesTable = $this->getMockBuilder(DBPayees::class)->disableOriginalConstructor()
            ->setMethods(['deletePayee', "doesPayeeIDExist"])->getMock();
    }

    public function test__construct(){

        parent::test__construct();
        $this->assertEquals($this->payeesTable, $this->request->getPayeesTable());
    }

    public function testExecute(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->payeesTable->expects($this->once())
            ->method('deletePayee')
            ->with($this->data["payee_id"]);
        $this->payeesTable->expects($this->once())
            ->method('doesPayeeIDExist')
            ->with()->will($this->returnValue(true));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    public function testExecuteWithInvalidPayee(){
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->payeesTable->expects($this->once())
            ->method('doesPayeeIDExist')
            ->with()
            ->will($this->returnValue(false));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("payee with ID", $response["ERROR_MESSAGE"]);
    }

    protected function createRequest(){
        $this->request = new DeletePayee($this->payeesTable, $this->usersTable, $this->user, $this->data);
    }
}
