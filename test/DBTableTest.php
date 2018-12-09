<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 8:09 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBTable.php');

require_once("TableCreationTest.php");

class DBTableTest extends TableCreationTest
{
    public function setUp()
    {
        $this->name = "test";
        $this->columns = ["ID" => "int(11)"];
        parent::setUp();
    }

    public function createTable(){
        $this->table = new \src\DBTable($this->database, $this->name);
    }

    public function testCouldNotConstructTableTwice(){

        try{
            $this->table = new \src\DBTable($this->database, $this->name);
            $this->assertTrue(false);
        }
        catch(Exception $e){
            $query = $this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ");
            $this->assertTrue($query !== FALSE);
        }
    }

    public function testDropTable(){
        $this->table->dropTable();
        $this->assertFalse($this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 "));
    }

    public function testDropTableTwiceShouldThrow(){
        $this->table->dropTable();
        try{
            $this->table->dropTable();
            $this->assertTrue(false);
        }
        catch(Exception $e){
            $query = $this->driver->query("SELECT 1 FROM ".$this->name." LIMIT 1 ");
            $this->assertFalse($query !== FALSE);
        }
    }
}
