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
    private $dbPayees;
    public function __construct($database, $dbPayees)
    {
        parent::__construct($database, "accounts");
        $this->dbPayees = $dbPayees;
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL,
                        PAYEE_ID int(11) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        CURRENT_AMOUNT int(11) DEFAULT 0,
                        PRIMARY KEY (ID)";
    }

    public function addAccount($name, $currentAmount, $payeeID){
        if($this->dbPayees->checkIfPayeeIDExists($payeeID) !== false){
            throw new \Exception("Couldn't insert account ".$name.", ".$currentAmount.", ".$payeeID.", "." in ".$this->name.
                ". Reason: Payee ID does not exist.");
        }
        if($this->doesAccountExists($name, $payeeID) !== false){
            throw new \Exception("Couldn't insert account ".$name.", ".$currentAmount.", ".$payeeID.", "." in ".$this->name.
                ". Reason: This account name already exists for this payee.");
        }
        $currentUTCDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $query = 'INSERT INTO '.$this->name.' (NAME, PAYEE_ID, ADDED_DATE, CURRENT_AMOUNT) VALUES ("'.
            $this->driver->real_escape_string($name).'", "'.$this->driver->real_escape_string($payeeID).'", "'.
            $currentUTCDate->format("Y-m-d H:i:s").'", "'.$this->driver->real_escape_string($currentAmount).'")';
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't insert account ".$name." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function getDBPayees(){
        return $this->dbPayees;
    }

    public function doesAccountExists($accountName, $payeeID){
        $result = $this->driver->query("SELECT ID FROM ".$this->name." WHERE NAME='".$this->driver->real_escape_string($accountName).
            "' AND PAYEE_ID='".$this->driver->real_escape_string($payeeID)."'");
        while($row = $result->fetch_assoc()){
            return true;
        }
        return false;
    }
}