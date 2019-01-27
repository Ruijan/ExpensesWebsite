<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:30 PM
 */

namespace BackEnd\Database;
require_once ("DBTable.php");

class DBPayees extends DBTable
{
    public function __construct($database)
    {
        parent::__construct($database, "payees");
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL UNIQUE,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        PRIMARY KEY (ID)";
    }

    public function addPayee($name){
        $currentUTCDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $query = 'INSERT INTO '.$this->name.' (NAME, ADDED_DATE) VALUES ("'.
            $this->driver->real_escape_string($name).'", "'.$currentUTCDate->format("Y-m-d H:i:s").'")';
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't insert payee ".$name." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
    }

    public function checkIfPayeeExists($expectedPayeeID){
        $query = "SELECT ID FROM ".$this->name." WHERE ID = ".$this->driver->real_escape_string($expectedPayeeID);
        $result = $this->driver->query($query);
        if ($result === FALSE ) {
            throw new \Exception("Couldn't find payee with ID ".$expectedPayeeID." in ".$this->name.". Reason: ".$this->driver->error_list[0]["error"]);
        }
        else if($result->num_rows == 0){
            return false;
        }
        return $result->fetch_assoc()["ID"];
    }

    public function getPayeeFromID($payeeID){
        $query = "SELECT * FROM ".$this->name." WHERE ID = ".$this->driver->real_escape_string($payeeID);
        $row = $this->driver->query($query)->fetch_assoc();
        return $row;
    }
}