<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/26/2019
 * Time: 6:32 PM
 */

namespace BackEnd\Database\DBExpenseStates;

use BackEnd\Database\DBTable;
use BackEnd\Database\InsertionException;

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

    /**
     * @param $name
     * @return mixed
     * @throws InsertionException
     */
    public function addState($name)
    {
        $this->checkParameters($name);
        $query = 'INSERT INTO ' . $this->name . ' (NAME) VALUES ("' .
            $this->driver->real_escape_string($name) . '")';
        if ($this->driver->query($query) === FALSE) {
            throw new InsertionException("expense", $name, $this->name, $this->driver->error);
        }
        return $this->driver->insert_id;
    }

    protected function checkParameters($name)
    {
        $query = 'SELECT * FROM ' . $this->name . ' WHERE NAME = "' .
            $this->driver->real_escape_string($name) . '"';
        $results = $this->driver->query($query);
        if ($results->num_rows == 1) {
            throw new InsertionException("expense", array("name" => $name), $this->name, "State name already exists.");
        }
    }

    public function getAllExpenseStates()
    {
        $query = "SELECT * FROM " . $this->getName();
        $result = $this->driver->query($query);
        $expenseStates = [];
        while ($result and $row = $result->fetch_assoc()) {
            $expenseStates[] = $row;
        }
        return $expenseStates;
    }

    /**
     * @param $stateID
     * @throws UndefinedExpenseStateID
     */
    public function deleteState($stateID){
        $this->checkIfIDExists($stateID);
        $query = "DELETE FROM " . $this->name . " WHERE ID='" . $this->driver->real_escape_string($stateID) . "'";
        $this->driver->query($query);
    }

    public function getExpenseStateFromID($id){
        $result = $this->driver->query("SELECT * FROM ". $this->name." WHERE ID = '".$this->driver->real_escape_string($id)."'")->fetch_assoc();
        return $result;
    }

    /**
     * @param $stateID
     * @throws UndefinedExpenseStateID
     */
    public function checkIfIDExists($stateID)
    {
        if (!$this->doesExpenseStateIDExist($stateID)) {
            throw new UndefinedExpenseStateID($stateID);
        }
    }

    public function doesExpenseStateIDExist($stateID)
    {
        $query = "SELECT ID FROM " . $this->name . " WHERE ID = " . $this->driver->real_escape_string($stateID);
        $result = $this->driver->query($query);
        return $result != false and $result->num_rows != 0;
    }
}