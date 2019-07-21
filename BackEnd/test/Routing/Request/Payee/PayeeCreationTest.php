<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:06 AM
 */
use BackEnd\Routing\Request\Payee\PayeeCreation;
use \BackEnd\Tests\Routing\Request\ConnectedRequestTest;

class PayeeCreationTest extends ConnectedRequestTest
{
    private $payeesTable;

    public function setUp()
    {
        $this->data = array("name" => "Migros");
        parent::setUp();
        $this->mandatoryFields[] = "name";
        $this->payeesTable = $this->getMockBuilder(\BackEnd\Database\DBCategories\DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['addPayee'])->getMock();
    }

    public function test__construct()
    {
        parent::test__construct();
        $this->assertEquals($this->payeesTable, $this->request->getPayeesTable());
    }

    public function testExecute()
    {
        $this->createRequest();
        $this->connectSuccessfullyUser();
        $this->payeesTable->expects($this->once())
            ->method('addPayee');
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    protected function createRequest()
    {
        $this->request = new PayeeCreation($this->payeesTable, $this->usersTable, $this->user, $this->data);
    }

}
