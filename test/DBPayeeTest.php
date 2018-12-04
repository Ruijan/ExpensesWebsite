<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:13 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBPayee.php');
require_once("TableCreationTest.php");

class DBPayeeTest extends TableCreationTest
{
    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "ADDED_DATE" => "datetime"];
        $this->name = "payees";
    }

    public function createTable()
    {
        $this->table = new \src\DBPayee($this->database);
    }
}
