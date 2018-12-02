<?php
namespace src;

class Database
{
    protected $driver;
    protected $databaseName;
    public function __construct($driver, $databaseName)
    {
        $this->driver = $driver;
        $this->databaseName = $databaseName;
        $query = "CREATE DATABASE IF NOT EXISTS ".$databaseName.";";
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't create database ".$databaseName.".");
        }
    }

    public function getDriver(){
        return $this->driver;
    }

    public function dropDatabase(){
        $query = "DROP DATABASE ".$this->databaseName.";";
        if ($this->driver->query($query) === FALSE) {
            throw new \Exception("Couldn't drop database ".$this->databaseName.".");
        }
    }
}

