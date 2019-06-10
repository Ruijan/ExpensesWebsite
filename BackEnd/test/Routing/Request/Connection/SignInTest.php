<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/7/2019
 * Time: 11:15 PM
 */

use BackEnd\Routing\Request\Connection\SignIn;
use PHPUnit\Framework\TestCase;

class SignInTest extends TestCase
{
    private $usersTable;
    private $user;
    private $data;

    public function setUp()
    {
        $this->data = array("email" => "testEmail@gmail.com", "password" => "13464awd6a43123w");
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['areCredentialsValid'])->getMock();
        $this->user = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connect', 'getFirstName',
                'getLastName', 'getID', 'getEmail', 'getSessionID'])->getMock();
    }

    public function test__construct(){
        $mandatoryFields = ["email", "password"];
        $request = new SignIn($this->usersTable, $this->user, $this->data);
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->usersTable, $request->getUsersTable());
        $this->assertEquals($this->user, $request->getUser());
    }


    public function testInitializationWithMissingParameters()
    {
        $this->data = array();
        $request = new SignIn($this->usersTable, $this->user, $this->data);
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }

    public function testGetResponse(){
        $request = new SignIn($this->usersTable, $this->user, $this->data);
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }    }

    public function testGetResponseWithInvalidUserID()
    {
        $request = new SignIn($this->usersTable, $this->user, $this->data);
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(false));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Invalid user", $response["ERROR_MESSAGE"]);
    }
}
