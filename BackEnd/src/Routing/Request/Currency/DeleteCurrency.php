<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/11/2019
 * Time: 9:01 PM
 */

namespace BackEnd\Routing\Request\Currency;


use BackEnd\Database\DBCurrencies\DBCurrencies;
use BackEnd\Database\DBCurrencies\UndefinedCurrencyException;
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\Routing\Request\Request;

class DeleteCurrency extends ConnectedRequest
{
    protected $name;
    protected $shortName;

    /** @var DBCurrencies */
    protected $currencyTable;

    public function __construct($currencyTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name", "short_name"];
        parent::__construct("DeleteCurrency", $mandatoryFields, $usersTable, $user, $data);
        $this->currencyTable = $currencyTable;
    }

    public function execute()
    {
        parent::execute();
        if($this->valid){
            $this->response = [];
            try{
                $this->checkIfCurrencyExists();
                $this->currencyTable->deleteCurrency($this->name, $this->shortName);
                $this->response["STATUS"] = "OK";
            }catch(UndefinedCurrencyException $exception){
                $this->buildResponseFromException($exception);
            }

            $this->response = json_encode($this->response);
        }

    }

    public function getCurrencyTable(){
        return $this->currencyTable;
    }

    protected function checkIfCurrencyExists(): void
    {
        $currencyExists = $this->currencyTable->doesCurrencyExist($this->name, $this->shortName);
        if (!$currencyExists) {
            throw new UndefinedCurrencyException($this->name, $this->shortName);
        }
    }
}