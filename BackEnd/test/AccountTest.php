<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/25/2019
 * Time: 9:25 PM
 */

require_once(str_replace("test", "src", __DIR__."/").'Account.php');

use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    private $accountName = "Savings";
    private $tableID = 1;
    private $currentAmount = 4452;
    public function test__construct(){
        $account = new \BackEnd\Account($this->accountName, $this->tableID, $this->currentAmount);
        $this->assertEquals($this->accountName, $account->getName());
        $this->assertEquals($this->currentAmount, $account->getCurrentAmount());
        $this->assertEquals($this->tableID, $account->getTableID());
    }

    public function test__constructWithDefaultAccountValue(){
        $account = new \BackEnd\Account($this->accountName, $this->tableID);
        $this->assertEquals(0, $account->getCurrentAmount());
    }
}
