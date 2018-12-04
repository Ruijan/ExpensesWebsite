<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 11:35 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'DBCategories.php');
require_once("TableCreationTest.php");

class DBCategoryTest extends TableCreationTest
{

    public function setUp(){
        parent::setUp();
        $this->columns = ["ID" => "int(11)",
            "NAME" => "char(50)",
            "PAYER_ID" => "int(11)",
            "ADDED_DATE" => "datetime"];
        $this->name = "categories";
    }

    public function createTable()
    {
        $this->table = new \src\DBCategories($this->database);
    }
}
