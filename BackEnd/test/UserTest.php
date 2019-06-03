<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/20/2019
 * Time: 1:51 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'User.php');
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $user;
    private $tableUsers;
    private $tableAccounts;
    private $password = "password";
    private $dbUser = ["FIRST_NAME" => "Julien",
        "NAME" => "Rechenmann",
        "EMAIL" => "jujudavid321@hotmail.com",
        "REGISTERED_DATE" => "",
        "LAST_CONNECTION" => "",
        "EMAIL_VALIDATED" => "1",
        "ID" => '154',
        "SESSION_ID" => '1234567891234567'];
    public function setUp()
    {
        @session_start();
        parent::setUp();
        $this->dbUser["REGISTERED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->dbUser["REGISTERED_DATE"] = $this->dbUser["REGISTERED_DATE"]->format("Y-m-d H:i:s");
        $this->dbUser["LAST_CONNECTION"] = $this->dbUser["REGISTERED_DATE"];
        $this->tableUsers = $this->getMockBuilder(\BackEnd\DBUser::class)->disableOriginalConstructor()
            ->setMethods(['areCredentialsValid', 'getUserFromEmail', 'getUserFromID',
                'disconnectUser', 'updateLastConnection', 'checkSessionID'])->getMock();
        $this->tableAccounts = $this->getMockBuilder(\BackEnd\DBAccount::class)->disableOriginalConstructor()
            ->setMethods(['getAccountsFromUserID'])->getMock();
    }

    public function test__constructWithoutSESSIONAndPOST(){
        $this->user = new \BackEnd\User();
        $this->assertFalse($this->user->isConnected());
    }

    public function testConnectWithSessionID(){
        $this->user = new \BackEnd\User();
        $this->prepareSuccessfulConnectionExpectations();
        $this->user->connectWithSessionID($this->tableUsers, $this->dbUser["SESSION_ID"], $this->dbUser["ID"]);
        $this->checkIfUserIsConnected();
    }

    public function testConnectUser(){
        $this->user = new \BackEnd\User();
        $this->tableUsers->expects($this->exactly(1))
            ->method('areCredentialsValid')->with($this->dbUser["EMAIL"], $this->password)->will($this->returnValue(true));
        $this->tableUsers->expects($this->once())
            ->method('getUserFromEmail')->with($this->dbUser["EMAIL"])->will($this->returnValue($this->dbUser));
        $now = new \DateTime("now", new \DateTimeZone("UTC"));
        $now = $now->format("Y-m-d H:i:s");
        $this->tableUsers->expects($this->once())
            ->method('updateLastConnection')->with($this->dbUser["ID"])->will($this->returnValue($now));
        $this->user->connect($this->tableUsers, $this->dbUser["EMAIL"], $this->password);
        $this->checkIfUserIsConnected();
    }

    public function testConnectingUserWhileIsAlreadyConnected(){
        $this->tableUsers->expects($this->exactly(0))
            ->method('areCredentialsValid');
        $this->tableUsers->expects($this->exactly(0))
            ->method('getUserFromEmail');
        $this->user = new \BackEnd\User();
        $this->prepareSuccessfulConnectionExpectations();
        $this->user->connectWithSessionID($this->tableUsers, $this->dbUser["SESSION_ID"], $this->dbUser["ID"]);
        $this->user->connect($this->tableUsers, "", "");
    }

    private function checkIfUserIsConnected(){
        $this->assertEquals($this->dbUser["FIRST_NAME"], $this->user->getFirstName());
        $this->assertEquals($this->dbUser["NAME"], $this->user->getLastName());
        $this->assertEquals($this->dbUser["REGISTERED_DATE"], $this->user->getRegistrationDate());
        $this->assertEquals($this->dbUser["LAST_CONNECTION"], $this->user->getLastConnectionDate());
        $this->assertEquals($this->dbUser["EMAIL_VALIDATED"], $this->user->isEmailValidated());
        $this->assertEquals($this->dbUser["ID"], $this->user->getID());
        $this->assertEquals($this->dbUser["EMAIL"], $this->user->getEmail());
        $this->assertEquals($this->dbUser["SESSION_ID"], $this->user->getSessionID());
        $this->assertTrue($this->user->isConnected());
    }

    public function testDisconnectUser(){
        $this->prepareSuccessfulConnectionExpectations();
        $this->user = new \BackEnd\User();
        $this->user->connectWithSessionID($this->tableUsers, $this->dbUser["SESSION_ID"], $this->dbUser["ID"]);
        $this->tableUsers->expects($this->once())
            ->method('disconnectUser')->with($this->dbUser["ID"]);
        $this->user->disconnect($this->tableUsers);
        $this->assertFalse($this->user->isConnected());
    }

    public function testLoadAccounts(){
        $this->tableAccounts->expects($this->exactly(1))
            ->method('getAccountsFromUserID')->with($this->dbUser["ID"])->will($this->returnValue([null]));
        $this->prepareSuccessfulConnectionExpectations();
        $this->user = new \BackEnd\User();
        $this->user->connectWithSessionID($this->tableUsers, $this->dbUser["SESSION_ID"], $this->dbUser["ID"]);
        $this->user->loadAccounts($this->tableAccounts);
        $this->assertEquals([null], $this->user->getAccounts());
    }

    public function testGetUserAsDictionary(){
        $this->prepareSuccessfulConnectionExpectations();
        $this->user = new \BackEnd\User();
        $this->user->connectWithSessionID($this->tableUsers, $this->dbUser["SESSION_ID"], $this->dbUser["ID"]);
        $this->assertEquals($this->dbUser, $this->user->asDict());
    }

    public function tearDown()
    {
        @session_destroy();
        parent::tearDown();
    }

    protected function prepareSuccessfulConnectionExpectations(): void
    {
        $this->tableUsers->expects($this->exactly(1))
            ->method('checkSessionID')->with($this->dbUser["SESSION_ID"], $this->dbUser["ID"])->will($this->returnValue(true));
        $this->tableUsers->expects($this->once())
            ->method('getUserFromID')->with($this->dbUser["ID"])->will($this->returnValue($this->dbUser));
        $now = new \DateTime("now", new \DateTimeZone("UTC"));
        $now = $now->format("Y-m-d H:i:s");
        $this->tableUsers->expects($this->once())
            ->method('updateLastConnection')->with($this->dbUser["ID"], $now, $this->dbUser["SESSION_ID"])->will($this->returnValue($now));
    }
}
