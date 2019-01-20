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
        "ID" => '154'];
    public function setUp()
    {
        @session_start();
        parent::setUp();
        $this->dbUser["REGISTERED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->dbUser["REGISTERED_DATE"] = $this->dbUser["REGISTERED_DATE"]->format("Y-m-d H:i:s");
        $this->dbUser["LAST_CONNECTION"] = $this->dbUser["REGISTERED_DATE"];
        $this->tableUsers = $this->getMockBuilder(\src\DBPayer::class)->disableOriginalConstructor()->setMethods(['connectUser', 'getUserFromEmail'])->getMock();
        $this->tableAccounts = $this->getMockBuilder(\src\DBAccount::class)->disableOriginalConstructor()->setMethods(['getAccountsFromUserID'])->getMock();
    }

    public function test__constructWithoutSESSIONAndPOST(){
        $this->user = new \src\User();
        $this->assertFalse($this->user->isConnected());
    }

    public function test__constructWithSession(){
        $this->initializeSession();
        $this->user = new \src\User();
        $this->checkIfUserIsConnected();
    }

    public function testSuccessfullyConnectUser(){
        $_POST["email"] = $this->dbUser["EMAIL"];
        $_POST["password"] = $this->password;
        $this->user = new \src\User();
        $this->user->disconnect();
        $this->tableUsers->expects($this->exactly(1))
            ->method('connectUser')->with($this->dbUser["EMAIL"], $this->password)->will($this->returnValue(true));
        $this->tableUsers->expects($this->once())
            ->method('getUserFromEmail')->with($this->dbUser["EMAIL"])->will($this->returnValue($this->dbUser));
        $this->user->connect($this->tableUsers);
        $this->checkIfUserIsConnected();
    }

    public function testConnectingUserWhileIsAlreadyConnected(){
        $this->tableUsers->expects($this->exactly(0))
            ->method('connectUser');
        $this->tableUsers->expects($this->exactly(0))
            ->method('getUserFromEmail');
        $this->initializeSession();
        $this->user = new \src\User();
        $this->user->connect($this->tableUsers);
    }

    private function initializeSession(){
        $_SESSION["FIRST_NAME"] = $this->dbUser["FIRST_NAME"];
        $_SESSION["NAME"] = $this->dbUser["NAME"];
        $_SESSION["REGISTERED_DATE"] = $this->dbUser["REGISTERED_DATE"];
        $_SESSION["LAST_CONNECTION"] = $this->dbUser["LAST_CONNECTION"];
        $_SESSION["EMAIL_VALIDATED"] = $this->dbUser["EMAIL_VALIDATED"];
        $_SESSION["EMAIL"] =  $this->dbUser["EMAIL"];
        $_SESSION["ID"] = $this->dbUser["ID"];
    }

    private function checkIfUserIsConnected(){
        $this->assertEquals($this->dbUser["FIRST_NAME"], $this->user->getFirstName());
        $this->assertEquals($this->dbUser["NAME"], $this->user->getLastName());
        $this->assertEquals($this->dbUser["REGISTERED_DATE"], $this->user->getRegistrationDate());
        $this->assertEquals($this->dbUser["LAST_CONNECTION"], $this->user->getLastConnectionDate());
        $this->assertEquals($this->dbUser["EMAIL_VALIDATED"], $this->user->isEmailValidated());
        $this->assertEquals($this->dbUser["ID"], $this->user->getID());
        $this->assertEquals($this->dbUser["EMAIL"], $this->user->getEmail());
        $this->assertEquals($this->dbUser, $_SESSION);
        $this->assertTrue($this->user->isConnected());
    }

    public function testDisconnectUser(){
        $this->initializeSession();
        $this->user = new \src\User();
        $this->user->disconnect();
        $this->assertFalse(isset($_SESSION["FIST_NAME"]));
        $this->assertFalse(isset($_SESSION["NAME"]));
        $this->assertFalse(isset($_SESSION["REGISTERED_DATE"]));
        $this->assertFalse(isset($_SESSION["LAST_CONNECTION"]));
        $this->assertFalse(isset($_SESSION["EMAIL_VALIDATED"]));
        $this->assertFalse(isset($_SESSION["ID"]));
        $this->assertFalse(isset($_SESSION["EMAIL"]));
        $this->assertFalse($this->user->isConnected());
        $this->assertEquals(null, $this->user->getFirstName());
        $this->assertEquals(null, $this->user->getLastName());
        $this->assertEquals(null, $this->user->getLastConnectionDate());
        $this->assertEquals(null, $this->user->getRegistrationDate());
        $this->assertEquals(null, $this->user->getRegistrationDate());
        $this->assertEquals(null, $this->user->getEmail());
        $this->assertEquals(null, $this->user->getID());
    }

    public function testLoadAccounts(){
        $this->tableAccounts->expects($this->exactly(1))
            ->method('getAccountsFromUserID')->with($this->dbUser["ID"])->will($this->returnValue([null]));
        $this->initializeSession();
        $this->user = new \src\User();
        $this->user->loadAccounts($this->tableAccounts);
        $this->assertEquals([null], $this->user->getAccounts());
    }

    public function testGetUserAsDictionary(){
        $this->initializeSession();
        $this->user = new \src\User();
        $this->assertEquals($this->dbUser, $this->user->asDict());
    }

    public function tearDown()
    {
        @session_destroy();
        parent::tearDown(); // TODO: Change the autogenerated stub
    }
}
