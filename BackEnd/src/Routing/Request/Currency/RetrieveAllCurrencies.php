<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/17/2019
 * Time: 10:10 PM
 */

namespace BackEnd\Routing\Request\Currency;

use BackEnd\Database\DBCurrencies\DBCurrencies;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Request;

class RetrieveAllCurrencies extends Request
{
    /** @var DBCurrencies */
    private $currenciesTable;
    public function __construct($currenciesTable, $data)
    {
        $mandatoryFields = [];
        parent::__construct($data, $mandatoryFields, "RetrieveCurrencies");
        $this->currenciesTable = $currenciesTable;
    }

    public function execute(){
        $this->response["DATA"] = $this->currenciesTable->getAllCurrencies();
        $this->response["STATUS"] = "OK";
        $this->response = json_encode($this->response);
    }

    public function getCurrenciesTable(){
        return $this->currenciesTable;
    }
}