<?php
namespace src;

class Database
{
    protected $driver;
    protected $databaseName;
    protected $tables = [];
    public function __construct($driver, $databaseName)
    {
        $this->driver = $driver;
        $this->databaseName = $databaseName;
        $query = "CREATE DATABASE IF NOT EXISTS ".$databaseName.";";
        $query = $this->driver->real_escape_string($query);
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't create database ".$databaseName.".");
        }
        $this->driver->select_db($this->databaseName);
    }

    public function getDriver(){
        return $this->driver;
    }

    public function exist(){
        return $this->driver->select_db($this->databaseName);
    }

    public function getDBName(){
        return $this->databaseName;
    }

    public function addTable($table, $name){
        $this->tables[$name] = $table;
    }

    public function getTableByName($name){
        return $this->tables[$name];
    }

    public function dropDatabase(){
        $query = "DROP DATABASE ".$this->databaseName.";";
        $query = $this->driver->real_escape_string($query);
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't drop database ".$this->databaseName.".");
        }
    }

    public function __destruct()
    {
        if($this->driver != null){
            $this->driver->close();
        }
    }
}

