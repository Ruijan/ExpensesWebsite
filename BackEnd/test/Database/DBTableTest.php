<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 8:09 PM
 */

namespace BackEnd\Tests\Database;
use BackEnd\Database\TableCreationException;
use BackEnd\Tests\Database\TableCreationTest;
use BackEnd\Database\DBTable;
use BackEnd\Database\TableDropException;

class DBTableTest extends TableCreationTest
{
    public function setUp()
    {
        $this->name = "test";
        $this->columns = ["ID" => "int(11)"];
        parent::setUp();
    }

    public function createTable(){

        $this->table = new DBTable($this->database, $this->name);
        $this->table->dropTable();
    }

    public function initTable(){
        $this->table->init();
    }

    public function testDropTable(){
        $this->table->dropTable();
        $this->assertFalse($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 "));
    }
}
