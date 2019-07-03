<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/15/2019
 * Time: 12:42 PM
 */

use BackEnd\Routing\Request\Category\CategoryCreation;
use PHPUnit\Framework\TestCase;

class CategoryCreationTest extends TestCase
{
    private $usersTable;
    private $categoriesTable;
    private $user;
    private $category;

    public function setUp()
    {
        $this->category = array("name" => "Food",
            "user_id" => 453,
            "session_id" => "1234567891234567");
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->categoriesTable = $this->getMockBuilder(\BackEnd\Database\DBCategories\DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['addCategory'])->getMock();
        $this->user = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connectWithSessionID'])->getMock();
    }
    public function test__construct()
    {
        $mandatoryFields = ["name", "session_id", "user_id"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->categoriesTable, $request->getCategoriesTable());
        $this->assertEquals($this->usersTable, $request->getUsersTable());
    }

    public function testExecute()
    {
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $this->user->expects($this->once())
            ->method('connectWithSessionID')
            ->with($this->usersTable, $this->category["session_id"], $this->category["user_id"]);
        $this->categoriesTable->expects($this->once())
            ->method('addCategory');
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertEquals("OK", $response["STATUS"]);
    }

    public function testGetResponseWithInvalidSession()
    {
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()
            ->will($this->returnValue(false));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Invalid user session", $response["ERROR_MESSAGE"]);
    }

    public function testInitializationWithMissingParameters()
    {
        $this->category = array();
        $request = $this->createRequest();
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }

    protected function createRequest()
    {
        $request = new CategoryCreation($this->categoriesTable, $this->usersTable, $this->user, $this->category);
        return $request;
    }

}
