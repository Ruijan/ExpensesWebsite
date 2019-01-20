<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/12/2019
 * Time: 4:46 PM
 */

namespace src;
require_once ("DBTable.php");

class DBAccount extends DBTable
{
    private $usersTable;
    public function __construct($database, $usersTable)
    {
        parent::__construct($database, "accounts");
        $this->usersTable = $usersTable;
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL,
                        PAYER_ID int(11) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        CURRENT_AMOUNT int(11) DEFAULT 0,
                        PRIMARY KEY (ID)";
    }

    public function addAccount($name, $currentAmount, $payerID){
        if($this->usersTable->checkIfIDExists($payerID) !== false){
            throw new \Exception("Couldn't insert account ".$name.", ".$currentAmount.", ".$payerID." in ".$this->name.
                ". Reason: Payer ID does not exist.");
        }
        if($this->doesAccountExists($name, $payerID) !== false){
            throw new \Exception("Couldn't insert account ".$name.", ".$currentAmount.", ".$payerID." in ".$this->name.
                ". Reason: This account name already exists for this payer.");
        }
        $currentUTCDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $query = 'INSERT INTO '.$this->name.' (NAME, PAYER_ID, ADDED_DATE, CURRENT_AMOUNT) VALUES ("'.
            $this->driver->real_escape_string($name).'", "'.$this->driver->real_escape_string($payerID).'", "'.
            $currentUTCDate->format("Y-m-d H:i:s").'", "'.$this->driver->real_escape_string($currentAmount).'")';
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't insert account ".$name." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    public function doesAccountExists($accountName, $payerID){
        $result = $this->driver->query("SELECT ID FROM ".$this->name." WHERE NAME='".$this->driver->real_escape_string($accountName).
            "' AND PAYER_ID='".$this->driver->real_escape_string($payerID)."'");
        while($result and $row = $result->fetch_assoc()){
            return true;
        }
        return false;
    }
}