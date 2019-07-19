<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/8/2019
 * Time: 8:42 PM
 */

namespace BackEnd\Routing\Request\Currency;

use BackEnd\Database\DBCurrencies\DBCurrencies;
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\Routing\Request\MissingParametersException;

class CurrencyCreation extends ConnectedRequest
{
    protected $name = "";
    protected $shortName = "";
    protected $currencyDollarsChange = "";
    /** @var DBCurrencies */
    protected $currenciesTable;

    public function __construct($currenciesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name", "short_name", "currency_dollars_change"];
        parent::__construct("CurrencyCreation", $mandatoryFields, $usersTable, $user, $data);
        $this->currenciesTable = $currenciesTable;
    }

    public function execute()
    {
        parent::execute();
        if($this->valid) {
            $this->response = [];
            try {
                $this->checkRequiredParameters();
                $id = $this->currenciesTable->addCurrency($this->name, $this->shortName);
                $this->response["STATUS"] = "OK";
                $this->response["DATA"] = array("CURRENCY_ID" => $id);
            } catch (\Exception | MissingParametersException $exception) {
                $this->buildResponseFromException($exception);
            }
            $this->response = json_encode($this->response);
        }
    }

    public function getCurrenciesTable(){
        return $this->currenciesTable;
    }
}