<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 7:29 PM
 */

use BackEnd\Routing\Request\Category\RetrieveAllSubCategories;
use PHPUnit\Framework\TestCase;
use BackEnd\Category;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\User;

class RetrieveAllSubCategoriesTest extends TestCase
{
    protected $categoriesTable;
    protected $usersTable;
    protected $user;
    protected $data;
    protected $category;

    public function setUp(){
        $this->category = $this->getMockBuilder(Category::class)->disableOriginalConstructor()
            ->setMethods(['asDict'])->getMock();
        $this->data = array("user_id" => 453,
            "session_id" => "1234567891234567");
        $this->usersTable = $this->getMockBuilder(DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['isUserSessionKeyValid'])->getMock();
        $this->categoriesTable = $this->getMockBuilder(DBCategories::class)->disableOriginalConstructor()
            ->setMethods(['getAllCategories'])->getMock();
        $this->user = $this->getMockBuilder(User::class)->disableOriginalConstructor()
            ->setMethods(['isConnected', 'connectWithSessionID'])->getMock();
    }

    public function test__construct(){
        $mandatoryFields = ["session_id", "user_id"];
        $request = $this->createRequest();
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
        $this->assertEquals($this->categoriesTable, $request->getCategoriesTable());
        $this->assertEquals($this->usersTable, $request->getUsersTable());
    }

    public function testExecute()
    {
        $category = array(
            "name" => "Food",
            "user_id" => 2,
            "added_date" => "2019-06-12 00:00:00"
        );
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()->will($this->returnValue(true));
        $this->user->expects($this->once())
            ->method('connectWithSessionID')
            ->with($this->usersTable, $this->data["session_id"], $this->data["user_id"]);
        $this->category->expects($this->once())
            ->method('asDict')
            ->with()->will($this->returnValue($category));
        $this->categoriesTable->expects($this->once())
            ->method('getAllCategories')
            ->with()->will($this->returnValue(array($this->category)));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        if($response["STATUS"] == "ERROR"){
            $this->assertEquals("", $response["ERROR_MESSAGE"]);
            $this->assertEquals("OK", $response["STATUS"]);
        }
        else{
            $this->assertEquals("OK", $response["STATUS"]);
        }
    }

    public function testGetResponseWithInvalidSession(){
        $request = $this->createRequest();
        $this->user->expects($this->once())
            ->method('isConnected')
            ->with()
            ->will($this->returnValue(false));
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true);
        $this->assertContains("Invalid user", $response["ERROR_MESSAGE"]);
        $this->assertEquals("ERROR", $response["STATUS"]);
    }

    public function testInitializationWithMissingParameters()
    {
        $this->data = array();
        $request = $this->createRequest();
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }

    protected function createRequest(){
        $request = new RetrieveAllSubCategories($this->categoriesTable, $this->usersTable, $this->user, $this->data);
        return $request;
    }
}
