<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 6:32 PM
 */

namespace BackEnd\Database\DBExpenseStates;

use BackEnd\Database\DBTable;
use BackEnd\Database\DBExpenseStates\InsertionException;

class DBExpenseStates extends DBTable
{
    public function __construct($database)
    {
        parent::__construct($database, "expense_states");
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL UNIQUE";
    }

    public function addState($name)
    {
        $this->checkParameters($name);
        $query = 'INSERT INTO ' . $this->name . ' (NAME) VALUES ("' .
            $this->driver->real_escape_string($name) . '")';
        if ($this->driver->query($query) === FALSE) {
            throw new InsertionException($name, $this->name, $this->driver->error);
        }
    }

    protected function checkParameters($name)
    {
        $query = 'SELECT COUNT(1) FROM ' . $this->name . ' WHERE NAME = "' .
            $this->driver->real_escape_string($name) . '"';
        $results = $this->driver->query($query);
        if ($results->num_rows == 0) {
            throw new InsertionException($name, $this->name, "State name already exists.");
        }
    }

    public function getExpenseStateFromID($id){
        $result = $this->driver->query("SELECT * FROM ". $this->name." WHERE ID = '".$this->driver->real_escape_string($id)."'")->fetch_assoc();
        return $result;
    }
}