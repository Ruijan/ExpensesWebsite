<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/20/2019
 * Time: 1:51 PM
 */

namespace src;


class User
{
    private $firstName;
    private $lastName;
    private $id;
    private $email;
    private $lastConnection;
    private $registrationDate;
    private $emailValidated;
    private $connected = false;
    private $accounts = [];

    public function __construct()
    {
        if(isset($_SESSION) and isset($_SESSION["EMAIL"]) and isset($_SESSION["FIRST_NAME"]) and isset($_SESSION["NAME"])
            and isset($_SESSION["ID"]) and isset($_SESSION["REGISTERED_DATE"]) and isset($_SESSION["LAST_CONNECTION"])
            and isset($_SESSION["EMAIL_VALIDATED"])){
            $this->fillUserFromArray($_SESSION);
            $this->connected = true;
        }
    }

    public function connect($userTable){
        if(!$this->connected and $userTable->connectUser($_POST["email"], $_POST["password"])){
            $dbUser = $userTable->getUserFromEmail($_POST["email"]);
            $this->fillUserFromArray($dbUser);
            $this->initializeSession();
            $this->connected = true;
        }
    }

    public function disconnect(){
        $this->connected = false;
        $this->lastName = null;
        $this->firstName = null;
        $this->lastConnection = null;
        $this->registrationDate = null;
        $this->emailValidated = null;
        $this->id = null;
        $this->email = null;
        unset($_SESSION["FIRST_NAME"]);
        unset($_SESSION["NAME"]);
        unset($_SESSION["REGISTERED_DATE"]);
        unset($_SESSION["LAST_CONNECTION"]);
        unset($_SESSION["EMAIL_VALIDATED"]);
        unset($_SESSION["EMAIL"]);
        unset($_SESSION["ID"]);
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
    }

    private function initializeSession(){
        $_SESSION["FIRST_NAME"] = $this->firstName;
        $_SESSION["NAME"] = $this->lastName;
        $_SESSION["REGISTERED_DATE"] = $this->registrationDate;
        $_SESSION["LAST_CONNECTION"] = $this->lastConnection;
        $_SESSION["EMAIL_VALIDATED"] = $this->emailValidated;
        $_SESSION["EMAIL"] = $this->email;
        $_SESSION["ID"] = $this->id;
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

    public function asDict(){
        return ["FIRST_NAME" => $this->firstName, "NAME" => $this->lastName, "EMAIL" => $this->email, "ID" => $this->id,
            "EMAIL_VALIDATED" => $this->emailValidated, "LAST_CONNECTION" => $this->lastConnection,
            "REGISTERED_DATE" => $this->registrationDate];
    }
}