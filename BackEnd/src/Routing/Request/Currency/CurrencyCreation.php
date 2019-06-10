<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/8/2019
 * Time: 8:42 PM
 */

namespace BackEnd\Routing\Request\Currency;

use BackEnd\Database\DBCurrencies;
use BackEnd\Routing\Request\Request;
use BackEnd\Routing\Request\MissingParametersException;

class CurrencyCreation extends Request
{
    protected $name = "";
    protected $shortName = "";
    protected $currencyDollarsChange = "";
    /** @var DBCurrencies */
    protected $currenciesTable;

    public function __construct($currenciesTable, $data)
    {
        $mandatoryFields = ["name", "short_name", "currency_dollars_change"];
        parent::__construct($data, $mandatoryFields);
        $this->currenciesTable = $currenciesTable;
    }

    public function execute()
    {
        try{
            $this->checkRequiredParameters();
            $id = $this->currenciesTable->addCurrency($this->name, $this->shortName);
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array("CURRENCY_ID" => $id);
        }
        catch(\Exception | MissingParametersException $e){
            $this->response["STATUS"] = "ERROR";
            $this->response["ERROR_MESSAGE"] = $e->getMessage();
        }
        $this->response = json_encode($this->response);
    }

    public function getCurrenciesTable(){
        return $this->currenciesTable;
    }
}