<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/16/2019
 * Time: 7:04 PM
 */

use BackEnd\Routing\Request\Connection\SignUp;
use PHPUnit\Framework\TestCase;
use BackEnd\Database\DBUsers\DBUsers;

class SignUpTest extends TestCase
{
    private $usersTable;
    private $user;
    private $dbUser;

    public function setUp()
    {
        $this->user = array("email" => "testEmail@gmail.com",
            "password" => "13464awd6a43123w",
            "last_name" => "testName",
            "first_name" => "testFirstName");
        $registeredDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $registeredFormattedDate = $registeredDate->format("Y-m-d H:i:s");
        $lastConnection = $registeredFormattedDate;
        $this->dbUser = array("EMAIL" => "testEmail@gmail.com",
            "PASSWORD" => "13464awd6a43123w",
            "LAST_NAME" => "testName",
            "FIRST_NAME" => "testFirstName",
            "REGISTERED_DATE" => $lastConnection,
            "LAST_CONNECTION" => $lastConnection,
            "VALIDATION_ID" => $registeredDate->getTimestamp());
        $this->usersTable = $this->getMockBuilder(\BackEnd\Database\DBUsers\DBUsers::class)->disableOriginalConstructor()
            ->setMethods(['addUser', 'getUserFromEmail'])->getMock();    }

    public function test__construct(){
        $mandatoryFields = ["email", "password", "first_name", "last_name"];
        $request = $this->createRequest();

        $this->assertEquals($this->dbUser["REGISTERED_DATE"], $request->getRegisteredDate());
        $this->assertEquals($this->dbUser["LAST_CONNECTION"], $request->getLastConnection());
        $this->assertTrue($this->dbUser["VALIDATION_ID"] - $request->getValidationID() < 10);
        $this->assertEquals($this->usersTable, $request->getUsersTable());
        $this->assertEquals($mandatoryFields, $request->getMandatoryFields());
    }

    public function test__constructWithMissingParameters()
    {
        $this->user = array();
        $request = $this->createRequest();
        $request->execute();
        $response = json_decode($request->getResponse(), $assoc = true );
        $this->assertEquals("ERROR", $response["STATUS"]);
        $this->assertContains("Missing parameter", $response["ERROR_MESSAGE"]);
        foreach ($request->getMandatoryFields() as $field) {
            $this->assertContains($field, $response["ERROR_MESSAGE"]);
        }
    }

    public function testGetResponse(){
        $request = $this->createRequest();
        $this->usersTable->expects($this->once())
            ->method('addUser');
        $this->usersTable->expects($this->once())
            ->method('getUserFromEmail')->will($this->returnValue($this->dbUser));
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

    /**
     * @return SignUp
     */
    protected function createRequest(): SignUp
    {
        $request = new SignUp($this->usersTable, $this->user);
        return $request;
    }
}
