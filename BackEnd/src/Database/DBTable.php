<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 8:05 PM
 */

namespace BackEnd\Database;

class DBTable
{
    protected $name;
    /** @var Database */
    protected $database;
    /** @var \mysqli */
    protected $driver;
    public function __construct($database, $name)
    {
        $this->database = $database;
        $this->driver = $database->getDriver();
        $this->name = $name;
    }

    public function init(){
        $this->createTable();
        $this->updateTableColumns();
    }

    protected function createTable(){
        $query = "CREATE TABLE ".$this->name." (".$this->getTableHeader().")";
        $this->driver->query($query);
    }

    public function updateTableColumns(){
        $columns = explode(',', $this->getTableHeader());
        foreach ($columns as $column){
            $columnName = explode(' ', ltrim ($column))[0];
            $parameters = substr(ltrim($column), strlen($columnName) + 1);
            if($columnName != "PRIMARY"){
                $query = "SHOW COLUMNS FROM `".$this->name."` LIKE '".$columnName."'";
                $result = $this->driver->query($query);
                if($result->num_rows == 0){
                    $query = "ALTER TABLE ".$this->name." ADD ".$columnName." ".$parameters;
                    $result = $this->driver->query($query);
                    if ($result === FALSE) {
                        throw new \Exception($this->driver->error_list[0]["error"]);
                    }
                }
            }
        }
    }

    public function retrieveAllRows(){
        $query = "SELECT * FROM ".$this->name;
        $result = $this->driver->query($query);
        $rows = [];
        while ($result and $row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getDatabase(){
        return $this->database;
    }

    public function getDriver(){
        return $this->driver;
    }
    public function getName(){
        return $this->name;
    }

    public function getTableHeader(){
        return "ID int(11) AUTO_INCREMENT UNIQUE";
    }

    public function dropTable(){
        $query = "DROP TABLE ".$this->name;
        $this->driver->query($query);
    }
}