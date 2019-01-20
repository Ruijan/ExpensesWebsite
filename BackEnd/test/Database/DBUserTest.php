<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:13 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBUser.php');
require_once("TableCreationTest.php");

class DBUserTest extends TableCreationTest
{
    private $payer = ["FIRST_NAME" => "Julien",
        "NAME" => "Rechenmann",
        "EMAIL" => "jujudavid321@hotmail.com",
        "PASSWORD" => "admin",
        "REGISTERED_DATE" => "",
        "LAST_CONNECTION" => "",
        "VALIDATION_ID" => '26457894'];
    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "FIRST_NAME" => "char(50)",
            "NAME" => "char(50)",
            "EMAIL" => "char(50)",
            "PASSWORD" => "char(50)",
            "REGISTERED_DATE" => "datetime",
            "LAST_CONNECTION" => "datetime",
            "VALIDATION_ID" => "int(11)",
            "EMAIL_VALIDATED" => "bit(1)"];
        $this->name = "payers";
        $this->payer["REGISTERED_DATE"] = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->payer["REGISTERED_DATE"] = $this->payer["REGISTERED_DATE"]->format("Y-m-d H:i:s");
        $this->payer["LAST_CONNECTION"] = $this->payer["REGISTERED_DATE"];
    }

    public function createTable()
    {
        $this->table = new \src\DBUser($this->database);
    }

    public function initTable(){
        $this->table->init();
    }

    public function testAddUser(){
        $this->table->addUser($this->payer);
        $result = $this->driver->query("SELECT * FROM ".$this->name)->fetch_assoc();
        $this->assertArraySubset($this->payer, $result, true);
    }

    public function testAddUserTwiceShouldThrow(){
        $this->table->addUser($this->payer);
        try{
            $this->table->addUser($this->payer);
        }
        catch (Exception $e){
            $count = 0;
            $result = $this->driver->query("SELECT * FROM ".$this->name);
            while($row = $result->fetch_assoc()){
                $this->assertArraySubset($this->payer, $row, true);
                $count += 1;
            }
            $this->assertEquals(1, $count);
            return;
        }
        $this->assertTrue(false);
    }

    public function testCheckIfPayerIDExist(){
        $this->table->addUser($this->payer);
        $expectedPayerID = 1;
        $payerID = $this->table->checkIfIDExists($expectedPayerID);
        $this->assertTrue($payerID);
    }

    public function testCheckIfPayerIDExistReturnFalse(){
        $this->table->addUser($this->payer);
        $expectedPayerID = 2;
        $payerID = $this->table->checkIfIDExists($expectedPayerID);
        $this->assertFalse($payerID);
    }

    public function testCheckIfPayerIDExistWithStringShouldThrow(){
        $this->table->addUser($this->payer);
        $expectedPayerID = "Palalal";
        $this->expectException(\Exception::class);
        $this->table->checkIfIDExists($expectedPayerID);
    }

    public function testCheckIfPayerEmailExist(){
        $this->table->addUser($this->payer);
        $expectedPayerID = 1;
        $payerID = $this->table->checkIfEmailExists($this->payer["EMAIL"]);
        $this->assertEquals($expectedPayerID, $payerID);
    }

    public function testCheckIfPayerEmailExistReturnFalse(){
        $this->table->addUser($this->payer);
        $payerID = $this->table->checkIfEmailExists("test@hotmail.com");
        $this->assertFalse($payerID);
    }

    public function testValidateEmailForPayer(){
        $this->table->addUser($this->payer);
        $this->table->validateEmail($this->payer["VALIDATION_ID"]);
        $result = $this->driver->query("SELECT EMAIL_VALIDATED FROM ".$this->name." WHERE VALIDATION_ID='".$this->payer["VALIDATION_ID"]."'");
        $row = $result->fetch_assoc();
        $this->assertEquals("1", $row["EMAIL_VALIDATED"]);
    }

    public function testValidateEmailWithWrongValidationIDShouldThrow(){
        $this->table->addUser($this->payer);
        $this->expectException(\Exception::class);
        $this->table->validateEmail(123456);
    }

    public function testValidCredentials(){
        $this->table->addUser($this->payer);
        $this->assertTrue($this->table->areCredentialsValid($this->payer["EMAIL"], $this->payer["PASSWORD"]));
    }

    public function testValidCredentialsWithWrongPassword(){
        $this->table->addUser($this->payer);
        $this->assertFalse($this->table->areCredentialsValid($this->payer["EMAIL"], "wrongPassword"));
    }

    public function testUpdateLastConnectionForUserID(){
        $this->table->addUser($this->payer);
        $this->payer["ID"] = 1;
        $this->payer["LAST_CONNECTION"] = "2018-01-25 20:05:45";
        $this->table->updateLastConnection($this->payer["ID"], $this->payer["LAST_CONNECTION"]);
        $result = $this->driver->query("SELECT LAST_CONNECTION FROM ".$this->name." WHERE ID='".$this->payer["ID"]."'");
        $row = $result->fetch_assoc();
        $this->assertEquals($this->payer["LAST_CONNECTION"], $row["LAST_CONNECTION"]);
    }

    public function testUpdateLastConnectionForWrongUserIDShouldThrow(){
        $this->table->addUser($this->payer);
        $this->payer["ID"] = 2;
        $newConnectionDate = "2018-01-25 20:05:45";
        $this->expectException(\Exception::class);
        $this->table->updateLastConnection($this->payer["ID"], $newConnectionDate);
    }

    public function testGetUserFromEmail(){
        $this->table->addUser($this->payer);
        $this->payer["ID"] = '1';
        $this->payer["EMAIL_VALIDATED"] = '0';
        $this->assertArraySubset($this->table->getUserFromEmail($this->payer["EMAIL"]), $this->payer, true);
    }
}
