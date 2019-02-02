<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/1/2019
 * Time: 8:23 PM
 */

namespace BackEnd\Loader;

use BackEnd\Loader\Loader;
use League\Csv\Reader;
use BackEnd\Expense;

class CSVLoader extends Loader
{
    private $fileName = "";
    private $reader;
    private $header;
    private $expenses = [];

    public function init($fileName)
    {
        $this->fileName = $fileName;
    }

    public function load()
    {
        $this->reader = Reader::createFromPath($this->fileName, 'r');
        $this->loadHeader();
        $this->loadExpenses();
    }

    public function loadHeader()
    {
        $this->reader->setHeaderOffset(0);
        $this->header = array_slice($this->reader->getHeader(), 0, 7);
        $this->header = $this->getExpenseEquivalentHeader($this->header);
    }

    protected function getExpenseEquivalentHeader($header)
    {
        $newHeader = [];
        foreach ($header as $element) {
            switch ($element){
                case "Date":
                    $newHeader[] = "expense_date";
                    break;
                case "Reason":
                    $newHeader[] = "sub_category";
                    break;
                case "Global reason":
                    $newHeader[] = "category";
                    break;
                case "Where":
                    $newHeader[] = "location";
                    break;
                case "Who":
                    $newHeader[] = "payee";
                    break;
                default:
                    $newHeader[] = $element;
                    break;
            }
        }
        return $newHeader;
    }

    protected function loadExpenses()
    {
        $records = $this->reader->getRecords($this->header);
        foreach ($records as $record) {
            $record = $this->setAmountForRecord($record);
            $this->expenses[] = new Expense($record);
        }
    }

    /**
     * @param $record
     * @return mixed
     */
    protected function setAmountForRecord($record)
    {
        if ($record["Out"] != NULL) {
            unset($record["In"]);
            $amount = $record["Out"];
            unset($record["Out"]);
            $record["amount"] = -$amount;
        } elseif ($record["In"] != NULL) {
            unset($record["Out"]);
            $amount = $record["In"];
            unset($record["In"]);
            $record["amount"] = $amount;
        }
        return $record;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getExpenses()
    {
        return $this->expenses;
    }
}