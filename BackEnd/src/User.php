<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/20/2019
 * Time: 1:51 PM
 */

namespace BackEnd;


use BackEnd\Database\DBUsers\DBUsers;

class User
{
    private $firstName;
    private $lastName;
    private $id;
    private $email;
    private $lastConnection;
    private $registrationDate;
    private $emailValidated;
    private $sessionID;
    private $connected = false;
    private $accounts = [];


    public function connectWithSessionID($userTable, $sessionID, $userID){
        if(!$this->connected and $userTable->isSessionIDValid($sessionID, $userID)){
            $dbUser = $userTable->getUserFromID($userID);
            $this->fillUserFromArray($dbUser);
            $this->updateLastConnection($userTable, $sessionID);
            $this->connected = true;
        }
    }

    public function connect($userTable, $email, $password){
        if(!$this->connected and $userTable->areCredentialsValid($email, $password)){
            $dbUser = $userTable->getUserFromEmail($email);
            $this->fillUserFromArray($dbUser);
            $this->sessionID = bin2hex(random_bytes(16));
            $this->updateLastConnection($userTable, $this->sessionID);
            $this->connected = true;
        }
    }

    public function updateLastConnection($userTable, $sessionID){
        $now = new \DateTime("now", new \DateTimeZone("UTC"));
        $now = $now->format("Y-m-d H:i:s");
        $userTable->updateLastConnection($this->id, $now, $sessionID);
    }

    public function disconnect($userTable){
        $userTable->disconnectUser($this->id);
        $this->connected = false;
        $this->lastName = null;
        $this->firstName = null;
        $this->lastConnection = null;
        $this->registrationDate = null;
        $this->emailValidated = null;
        $this->id = null;
        $this->email = null;
        $this->sessionID = null;
    }

    public function loadAccounts($tableAccounts){
        $this->accounts = $tableAccounts->getAccountsFromUserID($this->id);
    }

    private function fillUserFromArray($array){
        $this->lastName = $array["NAME"];
        $this->firstName = $array["FIRST_NAME"];
        $this->lastConnection = $array["LAST_CONNECTION"];
        $this->registrationDate = $array["REGISTERED_DATE"];
        $this->emailValidated = $array["EMAIL_VALIDATED"];
        $this->id = $array["ID"];
        $this->email = $array["EMAIL"];
        $this->sessionID = $array["SESSION_ID"];
    }

    public function isConnected(){
        return $this->connected;
    }

    public function getLastName(){
        return $this->lastName;
    }

    public function getFirstName(){
        return $this->firstName;
    }

    public function getLastConnectionDate(){
        return $this->lastConnection;
    }

    public function getRegistrationDate(){
        return $this->registrationDate;
    }
    public function isEmailValidated(){
        return $this->emailValidated;
    }

    public function getEmail(){
        return $this->email;
    }
    public function getID(){
        return $this->id;
    }

    public function getAccounts(){
        return $this->accounts;
    }

    public function getSessionID(){
        return $this->sessionID;
    }

    public function asDict(){
        return ["FIRST_NAME" => $this->firstName, "NAME" => $this->lastName, "EMAIL" => $this->email, "ID" => $this->id,
            "EMAIL_VALIDATED" => $this->emailValidated, "LAST_CONNECTION" => $this->lastConnection,
            "REGISTERED_DATE" => $this->registrationDate, "SESSION_ID" => $this->sessionID];
    }
}