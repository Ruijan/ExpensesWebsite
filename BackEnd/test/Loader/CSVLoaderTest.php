<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/1/2019
 * Time: 8:24 PM
 */

namespace Loader;

use BackEnd\Expense;
use BackEnd\Loader\CSVLoader;
use PHPUnit\Framework\TestCase;
use League\Csv\Reader;
use SplFileObject;

class CSVLoaderTest extends TestCase
{
    private $csvLoader;
    private $fileName = "Swiss.csv";
    private $header = ["Date", "Where", "Who", "Global reason", "Reason", "Out","In"];
    private $firstExpense;

    public function setUp()
    {
        $this->csvLoader = new CSVLoader();
        $this->firstExpense = new Expense(array("expense_date" => "25.12.2018",
            "location" => "Online",
            "payee" => "EPFL",
            "category" => "Income",
            "sub_category" => "Salary",
            "amount" => "3483.05"));
    }

    public function testInit(){
        $this->csvLoader->init($this->fileName);
        $this->assertEquals($this->fileName, $this->csvLoader->getFileName());
    }

    public function testLoad(){
        $this->csvLoader->init(__DIR__ .'\\'.$this->fileName);
        $this->csvLoader->load();
        $expenses = $this->csvLoader->getExpenses();
        $this->assertEquals(56, sizeof($expenses));
        $this->assertEquals($this->firstExpense, $expenses[0]);
    }
}
