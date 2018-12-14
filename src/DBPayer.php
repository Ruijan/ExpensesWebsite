<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:12 PM
 */

namespace src;
require_once ("DBTable.php");

class DBPayer extends DBTable
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
                        USERNAME char(50) NOT NULL UNIQUE,
                        PASSWORD char(50) NOT NULL,
                        REGISTERED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        LAST_CONNECTION datetime DEFAULT '2018-01-01 00:00:00',
                        PRIMARY KEY (ID)";
    }

    public function addPayer($payer){
        $values = [];
        $indexValue = 0;
        foreach ($payer as $value) {
            $values[$indexValue] = '"'.$this->driver->real_escape_string($value).'"';
            $indexValue += 1;
        }
        $values = implode(", ", $values);
        $query = 'INSERT INTO '.$this->name.
            ' (FIRST_NAME, NAME, EMAIL, USERNAME, PASSWORD, REGISTERED_DATE, LAST_CONNECTION) VALUES ('.
            $values.')';
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't insert payer ".implode(" ,", $payer)." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function checkIfPayerIDExists($expectedPayerID){
        $query = "SELECT ID FROM ".$this->name." WHERE ID = ".$this->driver->real_escape_string($expectedPayerID);
        $result = $this->driver->query($query);
        if ($result === FALSE ) {
            throw new \Exception("Couldn't find payee with ID ".$expectedPayerID." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
        else if($result->num_rows == 0){
            return false;
        }
        return $result->fetch_assoc()["ID"];
    }
    public function checkIfPayerEmailExists($email){
        $query = "SELECT ID FROM ".$this->name." WHERE EMAIL = '".$this->driver->real_escape_string($email)."'";
        $result = $this->driver->query($query);
        if ($result === FALSE ) {
            throw new \Exception("Couldn't find payee with ID ".$email." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
        else if($result->num_rows == 0){
            return false;
        }
        return $result->fetch_assoc()["ID"];
    }
}