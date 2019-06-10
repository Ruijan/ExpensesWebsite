<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:13 PM
 */

namespace BackEnd\Tests\Database\DBUsers;

use BackEnd\Database\DBUsers\UndefinedUserEmail;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Database\DBUsers\UndefinedUserID;
use BackEnd\Database\InsertionException;
use BackEnd\Database\DBUsers\EmailValidationException;

class DBUserTest extends TableCreationTest
{
    private $user = ["FIRST_NAME" => "Julien",
        "NAME" => "Rechenmann",
        "EMAIL" => "jujudavid321@hotmail.com",
        "PASSWORD" => "admin",
        "REGISTERED_DATE" => "",
        "LAST_CONNECTION" => "",
        "VALIDATION_ID" => '154'];

    public function setUp()
    {
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "FIRST_NAME" => "char(50)",
            "NAME" => "char(50)",
            "EMAIL" => "char(50)",
            "PASSWORD" => "char(50)",
            "REGISTERED_DATE" => "datetime",
            "LAST_CONNECTION" => "datetime",
            "VALIDATION_ID" => "int(11)",
            "EMAIL_VALIDATED" => "bit(1)",
            "SESSION_ID" => "char(50)"];
        $this->name = "payers";
        $this->user["REGISTERED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->user["REGISTERED_DATE"] = $this->user["REGISTERED_DATE"]->format("Y-m-d H:i:s");
        $this->user["LAST_CONNECTION"] = $this->user["REGISTERED_DATE"];
    }

    public function createTable()
    {
        $this->table = new DBUsers($this->database);
    }

    public function initTable()
    {
        $this->table->init();
    }

    public function testAddUser()
    {
        $this->table->addUser($this->user);
        $result = $this->driver->query("SELECT * FROM " . $this->name)->fetch_assoc();
        $this->assertArraySubset($this->user, $result, true);
    }

    public function testAddUserTwiceShouldThrow()
    {
        $this->table->addUser($this->user);
        try {
            $this->table->addUser($this->user);
        } catch (InsertionException $e) {
            $count = 0;
            $result = $this->driver->query("SELECT * FROM " . $this->name);
            while ($row = $result->fetch_assoc()) {
                $this->assertArraySubset($this->user, $row, true);
                $count += 1;
            }
            $this->assertEquals(1, $count);
            return;
        }
        $this->assertTrue(false);
    }

    public function testValidateEmailForPayer()
    {
        $this->table->addUser($this->user);
        $this->table->validateEmail($this->user["VALIDATION_ID"]);
        $result = $this->driver->query("SELECT EMAIL_VALIDATED FROM " . $this->name . " WHERE VALIDATION_ID='" . $this->user["VALIDATION_ID"] . "'");
        $row = $result->fetch_assoc();
        $this->assertEquals("1", $row["EMAIL_VALIDATED"]);
    }

    public function testValidateEmailWithWrongValidationIDShouldThrow()
    {
        $this->table->addUser($this->user);
        $this->expectException(EmailValidationException::class);
        $this->table->validateEmail(123456);
    }

    public function testValidCredentials()
    {
        $this->table->addUser($this->user);
        $this->assertTrue($this->table->areCredentialsValid($this->user["EMAIL"], $this->user["PASSWORD"]));
    }

    public function testValidCredentialsWithWrongPassword()
    {
        $this->table->addUser($this->user);
        $this->assertFalse($this->table->areCredentialsValid($this->user["EMAIL"], "wrongPassword"));
    }

    public function testValidSessionID()
    {
        $this->table->addUser($this->user);
        $this->user["ID"] = 1;
        $this->user["SESSION_ID"] = "0";
        $this->assertTrue($this->table->isSessionIDValid($this->user["SESSION_ID"], $this->user["ID"]));
    }

    public function testInvalidSessionID()
    {
        $this->table->addUser($this->user);
        $this->user["ID"] = 1;
        $this->user["SESSION_ID"] = "123456";
        $this->assertFalse($this->table->isSessionIDValid($this->user["SESSION_ID"], $this->user["ID"]));
    }

    public function testUpdateLastConnectionForUserID()
    {
        $this->table->addUser($this->user);
        $this->user["ID"] = 1;
        $this->user["LAST_CONNECTION"] = "2018-01-25 20:05:45";
        $this->user["SESSION_ID"] = bin2hex(random_bytes(16));
        $this->table->updateLastConnection($this->user["ID"], $this->user["LAST_CONNECTION"], $this->user["SESSION_ID"]);
        $result = $this->driver->query("SELECT LAST_CONNECTION, SESSION_ID FROM " . $this->name . " WHERE ID='" . $this->user["ID"] . "'");
        $row = $result->fetch_assoc();
        $this->assertEquals($this->user["LAST_CONNECTION"], $row["LAST_CONNECTION"]);
        $this->assertEquals($this->user["SESSION_ID"], $row["SESSION_ID"]);
    }

    public function testUpdateLastConnectionWithWrongDate()
    {
        $this->table->addUser($this->user);
        $this->user["ID"] = 1;
        $this->user["LAST_CONNECTION"] = "as1564w";
        $this->user["SESSION_ID"] = bin2hex(random_bytes(16));
        $this->expectException(\Exception::class);
        $this->table->updateLastConnection($this->user["ID"], $this->user["LAST_CONNECTION"], $this->user["SESSION_ID"]);

    }

    public function testUpdateLastConnectionForWrongUserIDShouldThrow()
    {
        $this->table->addUser($this->user);
        $this->user["ID"] = 2;
        $newConnectionDate = "2018-01-25 20:05:45";
        $this->user["SESSION_ID"] = bin2hex(random_bytes(16));
        $this->expectException(\Exception::class);
        $this->table->updateLastConnection($this->user["ID"], $newConnectionDate, $this->user["SESSION_ID"]);
    }

    public function testGetUserFromID()
    {
        $this->table->addUser($this->user);
        $expectedUser = $this->user;
        $expectedUser["ID"] = '1';
        $expectedUser["EMAIL_VALIDATED"] = '0';
        $expectedUser["SESSION_ID"] = '0';
        unset($expectedUser["PASSWORD"]);
        unset($expectedUser["VALIDATION_ID"]);
        $obtainedUser = $this->table->getUserFromID($expectedUser["ID"]);
        $this->assertArraySubset($expectedUser, $obtainedUser, true);
    }

    public function testGetUserFromWrongIDShouldThrow()
    {
        $this->table->addUser($this->user);
        $expectedPayerID = 2;
        $this->expectException(UndefinedUserID::class);
        $this->table->getUserFromID($expectedPayerID);
    }

    public function testCheckIfPayerIDExistWithStringShouldThrow()
    {
        $this->table->addUser($this->user);
        $expectedPayerID = "Palalal";
        $this->expectException(UndefinedUserID::class);
        $this->table->getUserFromID($expectedPayerID);
    }

    public function testGetUserFromEmail()
    {
        $this->table->addUser($this->user);
        $expectedUser = $this->user;
        $expectedUser["ID"] = '1';
        $expectedUser["EMAIL_VALIDATED"] = '0';
        $expectedUser["SESSION_ID"] = '0';
        unset($expectedUser["PASSWORD"]);
        unset($expectedUser["VALIDATION_ID"]);
        $obtainedUser = $this->table->getUserFromEmail($expectedUser["EMAIL"]);
        $this->assertArraySubset($expectedUser, $obtainedUser, true);
    }

    public function testCheckIfWrongPayerEmailExistShouldThrow()
    {
        $this->table->addUser($this->user);
        $this->expectException(UndefinedUserEmail::class);
        $this->table->getUserFromEmail("coucou@gmail.com");
    }

    public function testDeleteUser()
    {
        $this->table->addUser($this->user);
        $this->table->deleteUserFromEmail($this->user["EMAIL"]);
        $this->expectException(UndefinedUserEmail::class);
        $this->table->getUserFromEmail($this->user["EMAIL"]);
    }

    public function testDeleteUserWithWrongEmail()
    {
        $this->table->addUser($this->user);
        $this->expectException(UndefinedUserEmail::class);
        $this->table->deleteUserFromEmail("123456");
    }

    public function testDisconnectUser()
    {
        $this->table->addUser($this->user);
        $this->user["ID"] = 1;
        $this->user["LAST_CONNECTION"] = "2018-01-25 20:05:45";
        $this->user["SESSION_ID"] = bin2hex(random_bytes(16));
        $this->table->updateLastConnection($this->user["ID"], $this->user["LAST_CONNECTION"], $this->user["SESSION_ID"]);
        $this->table->disconnectUser($this->user["ID"]);
        $disconnectedUser = $this->table->getUserFromID($this->user["ID"]);
        $this->assertEquals("", $disconnectedUser["SESSION_ID"]);
    }

    public function testDisconnectUserWithWrongUserID()
    {
        $this->table->addUser($this->user);
        $this->user["ID"] = 1;
        $this->user["LAST_CONNECTION"] = "2018-01-25 20:05:45";
        $this->user["SESSION_ID"] = bin2hex(random_bytes(16));
        $this->table->updateLastConnection($this->user["ID"], $this->user["LAST_CONNECTION"], $this->user["SESSION_ID"]);
        $this->expectException(UndefinedUserID::class);
        $this->user["ID"] = 4;
        $this->table->disconnectUser($this->user["ID"]);
    }
}
