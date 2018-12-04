<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/4/2018
 * Time: 9:12 PM
 */

namespace src;
require_once ("DBTable.php");

class DBPayer extends DBTable
{
    public function __construct($database)
    {
        parent::__construct($database, "payers");
    }

    public function getTableHeader()
    {
        return "ID int(11) AUTO_INCREMENT UNIQUE,
                        FIRST_NAME char(50) NOT NULL,
                        NAME char(50) NOT NULL,
                        EMAIL char(50) NOT NULL,
                        USERNAME char(50) NOT NULL,
                        PASSWORD char(50) NOT NULL,
                        REGISTERED_DATE datetime DEFAULT '2018-01-01 00:00:00',
                        LAST_CONNECTION datetime DEFAULT '2018-01-01 00:00:00',
                        PRIMARY KEY (ID)";
    }
}