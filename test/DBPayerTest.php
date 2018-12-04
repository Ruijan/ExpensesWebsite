<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:13 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBPayer.php');
require_once("TableCreationTest.php");

class DBPayerTest extends TableCreationTest
{
    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "FIRST_NAME" => "char(50)",
            "NAME" => "char(50)",
            "EMAIL" => "char(50)",
            "USERNAME" => "char(50)",
            "PASSWORD" => "char(50)",
            "REGISTERED_DATE" => "datetime",
            "LAST_CONNECTION" => "datetime"];
        $this->name = "payers";
    }

    public function createTable()
    {
        $this->table = new \src\DBPayer($this->database);
    }
}
