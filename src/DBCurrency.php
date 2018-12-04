<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 10:08 PM
 */

namespace src;
require_once ("DBTable.php");

class DBCurrency extends DBTable
{
    public function __construct($database)
    {
        parent::__construct($database, "currencies");
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        NAME char(50) NOT NULL,
                        CURRENT_DOLLARS_CHANGE int(11) NOT NULL,
                        PRIMARY KEY (ID)";
    }
}