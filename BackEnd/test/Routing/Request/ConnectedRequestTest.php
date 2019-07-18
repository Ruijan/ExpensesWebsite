<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/18/2019
 * Time: 9:39 PM
 */
namespace BackEnd\Tests\Routing\Request;

abstract class ConnectedRequestTest extends RequestTest
{
    protected $usersTable;
    protected $user;
    protected $mandatoryFields;
    protected $request;

    public function setUp()
    {
        $this->mandatoryFields = ["session_id", "user_id"];
        $this->data["user_id"] = 453;
        $this->data["session_id"] = "1234567891234567";
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->user = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connectWithSessionID'])->getMock();
    }

    public function test__construct(){
        $this->createRequest();
        if(sizeof($this->mandatoryFields) == 0){
            $this->assertNull($this->request->getMandatoryFields());
        }
        else{
            $mandatoryFields = $this->request->getMandatoryFields();
            sort($this->mandatoryFields);
            sort($mandatoryFields);
            $this->assertEquals($this->mandatoryFields, $mandatoryFields);
        }
        $this->assertEquals($this->usersTable, $this->request->getUsersTable());
    }

    public function testGetResponseWithInvalidSession()
    {
        $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()
            ->will($this->returnValue(false));
        $this->request->execute();
        $response = json_decode($this->request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Invalid user session", $response["ERROR_MESSAGE"]);
    }
}
