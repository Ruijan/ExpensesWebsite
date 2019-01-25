<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/25/2019
 * Time: 9:25 PM
 */

namespace src;


class Account
{
    private $name;
    private $currentAmount;
    private $tableID;

    public function __construct($name, $tableID, $currentAmount = 0)
    {
        $this->currentAmount = $currentAmount;
        $this->name = $name;
        $this->tableID = $tableID;
    }

    public function getName(){
        return $this->name;
    }

    public function getCurrentAmount(){
        return $this->currentAmount;
    }

    public function getTableID(){
        return $this->tableID;
    }
}