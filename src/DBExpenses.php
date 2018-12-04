<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 5:17 PM
 */

namespace src;
require_once ("DBTable.php");

class DBExpenses extends DBTable
{
    public function __construct($database){
        parent::__construct($database, "expenses");
    }

    public function getTableHeader(){
        return "ID int(11) AUTO_INCREMENT UNIQUE,
            LOCATION char(50) NOT NULL,
            PAYER_ID int(11) NOT NULL,
            PAYEE_ID int(11) NOT NULL,
            CATEGORY_ID int(11) NOT NULL,
            SUB_CATEGORY_ID int(11) NOT NULL,
            ADDED_DATE datetime DEFAULT '2018-01-01 00:00:00',
            EXPENSE_DATE datetime DEFAULT '2018-01-01 00:00:00',
            AMOUNT double NULL,
            CURRENCY_ID int(11) NOT NULL,
            STATE int NULL,
            PRIMARY KEY  (ID)";
    }
}