<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:14 PM
 */

namespace src;
require_once ("DBTable.php");

class DBSubCategories extends DBTable
{
    public function __construct($database){
        parent::__construct($database, "sub_categories");
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        PARENT_ID int(11) NOT NULL,
                        NAME char(50) NOT NULL,
                        PAYER_ID int(11) NOT NULL,
                        ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        PRIMARY KEY (ID)";
    }
}