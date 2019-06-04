<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:12 PM
 */

namespace BackEnd\Database\DBUsers;
use BackEnd\Database\DBUsers\UndefinedUserID;
use BackEnd\Database\DBTable;
use BackEnd\Database\DBUsers\InsertionException;
use BackEnd\Database\DBUsers\EmailValidationException;

class DBUsers extends DBTable
{
    public function __construct($database)
    {
        parent::__construct($database, "payers");
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        FIRST_NAME char(50) NOT NULL,
                        NAME char(50) NOT NULL,
                        EMAIL char(50) NOT NULL UNIQUE,
                        PASSWORD char(50) NOT NULL,
                        REGISTERED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        LAST_CONNECTION datetime DEFAULT '2018-01-01 00:00:00',
                        VALIDATION_ID int(11) UNIQUE,
                        EMAIL_VALIDATED bit DEFAULT 0,
                        SESSION_ID char(50) DEFAULT 0,
                        PRIMARY KEY (ID)";
    }

    public function addUser($user)
    {
        $values = [];
        $indexValue = 0;
        foreach ($user as $value) {
            $values[$indexValue] = '"' . $this->driver->real_escape_string($value) . '"';
            $indexValue += 1;
        }
        $values = implode(", ", $values);
        $query = 'INSERT INTO ' . $this->name .
            ' (FIRST_NAME, NAME, EMAIL, PASSWORD, REGISTERED_DATE, LAST_CONNECTION, VALIDATION_ID) VALUES (' .
            $values . ')';
        if ($this->driver->query($query) === FALSE) {
            throw new InsertionException($user, $this->name, $this->driver->error_list[0]["error"]);
        }
    }

    public function validateEmail($validationID)
    {
        $result = $this->driver->query("SELECT EMAIL_VALIDATED FROM " . $this->name . " WHERE VALIDATION_ID='" . $this->driver->real_escape_string($validationID) . "'");
        $row = $result->fetch_assoc();
        if (!$row or $row["EMAIL_VALIDATED"] == "1") {
            throw new EmailValidationException($this->driver->real_escape_string($validationID),
                $this->name,
                "Either the ID is invalid or this email address has already been validated.");
        }
        $query = "UPDATE " . $this->name . " SET EMAIL_VALIDATED = 1 WHERE VALIDATION_ID = '" . $this->driver->real_escape_string($validationID) . "'";
        if ($this->driver->query($query) === FALSE) {
            throw new EmailValidationException($this->driver->real_escape_string($validationID),
                $this->name,
                $this->driver->error_list[0]["error"]);
        }
    }

    public function areCredentialsValid($email, $password)
    {
        $result = $this->driver->query("SELECT ID FROM " . $this->name . " WHERE EMAIL='" . $this->driver->real_escape_string($email) .
            "' AND PASSWORD='" . $this->driver->real_escape_string($password) . "'");
        $row = $result->fetch_assoc();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function isSessionIDValid($sessionID, $userID)
    {
        $result = $this->driver->query("SELECT ID FROM " . $this->name . " WHERE SESSION_ID='" . $this->driver->real_escape_string($sessionID) .
            "' AND ID='" . $this->driver->real_escape_string($userID) . "'");
        $row = $result->fetch_assoc();
        if (!$row) {
            return false;
        }
        return true;
    }

    public function updateLastConnection($userID, $lastConnection, $sessionID)
    {
        $this->checkIfIDExists($userID);
        $query = "UPDATE " . $this->name . " SET LAST_CONNECTION = '" . $this->driver->real_escape_string($lastConnection) .
            "', SESSION_ID = '". $this->driver->real_escape_string($sessionID) .  "' WHERE ID = '" . $this->driver->real_escape_string($userID) . "'";
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't update last connection for userid " . $this->driver->real_escape_string($userID) . " in " . $this->name . ". Reason: " . $this->driver->error_list[0]["error"]);
        }
    }

    protected function checkIfIDExists($expectedPayerID)
    {
        $query = "SELECT ID FROM " . $this->name . " WHERE ID = " . $this->driver->real_escape_string($expectedPayerID);
        $result = $this->driver->query($query);
        if ($result === FALSE) {
            throw new \Exception("Couldn't find payee with ID " . $expectedPayerID . " in " . $this->name . ". Reason: " . $this->driver->error_list[0]["error"]);
        } else if ($result->num_rows == 0) {
            throw new UndefinedUserID($expectedPayerID);
        }
    }

    public function getUserFromEmail($email)
    {
        if($this->checkIfEmailExists($email) === FALSE){
            throw new UndefinedUserEmail($email);
        }
        $result = $this->driver->query("SELECT ID, FIRST_NAME, NAME, REGISTERED_DATE, LAST_CONNECTION, EMAIL_VALIDATED, EMAIL, SESSION_ID  FROM " .
            $this->name . " WHERE EMAIL='" . $this->driver->real_escape_string($email) . "'");
        return $result->fetch_assoc();
    }

    public function checkIfEmailExists($email)
    {
        $query = "SELECT ID FROM " . $this->name . " WHERE EMAIL = '" . $this->driver->real_escape_string($email) . "'";
        $result = $this->driver->query($query);
        if ($result->num_rows == 0) {
            return FALSE;
        }
        return $result->fetch_assoc()["ID"];
    }

    public function getUserFromID($userID)
    {
        $this->checkIfIDExists($userID);
        $result = $this->driver->query("SELECT ID, FIRST_NAME, NAME, REGISTERED_DATE, LAST_CONNECTION, EMAIL_VALIDATED, EMAIL, SESSION_ID  FROM " .
            $this->name . " WHERE ID='" . $this->driver->real_escape_string($userID) . "'");
        return $result->fetch_assoc();
    }

    public function disconnectUser($userID){
        $this->checkIfIDExists($userID);
        $query = "UPDATE " . $this->name . " SET SESSION_ID = '' WHERE ID = '" . $this->driver->real_escape_string($userID) . "'";
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't disconnect user with userid " . $this->driver->real_escape_string($userID) . " in " . $this->name . ". Reason: " . $this->driver->error_list[0]["error"]);
        }
    }
}