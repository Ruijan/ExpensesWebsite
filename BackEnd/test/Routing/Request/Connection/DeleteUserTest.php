<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/9/2019
 * Time: 5:16 PM
 */

use BackEnd\Routing\Request\Connection\DeleteUser;
use PHPUnit\Framework\TestCase;

class DeleteUserTest extends TestCase
{
    private $user;
    private $usersTable;

    public function setUp()
    {
        $this->user = array("email" => "test@example.com",
            "password" => "1d23a456");
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['areCredentialsValid', 'deleteUserFromEmail'])->getMock();
    }

    public function testGetResponse()
    {
        $request = new DeleteUser($this->usersTable, $this->user);
        $this->usersTable->expects($this->once())
            ->method('areCredentialsValid')
            ->with()->will($this->returnValue(true));
        $this->usersTable->expects($this->once())
            ->method('deleteUserFromEmail');
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    public function test__construct()
    {
        $mandatoryFields = ["email", "password"];
        $request = new DeleteUser($this->usersTable, $this->user);
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->usersTable, $request->getUsersTable());
    }

    public function testInitializationWithMissingParameters()
    {
        $this->user = array();
        $request = new DeleteUser($this->usersTable, $this->user);
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }
}
