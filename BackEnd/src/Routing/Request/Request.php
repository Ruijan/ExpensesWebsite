<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 8:58 PM
 */

namespace BackEnd\Routing\Request;

abstract class Request extends ArrayToPropertiesSetter
{
    protected $response;
    protected $mandatoryFields;
    public function __construct($data, $mandatoryFields){
        parent::__construct($data);
        $this->mandatoryFields = $mandatoryFields;
    }

    abstract public function execute();

    protected function checkRequiredParameters(): void
    {
        $missingFields = $this->getMissingFields($this->mandatoryFields);
        if (count($missingFields) > 0) {
            throw new MissingParametersException($missingFields, "AccountCreation");
        }
    }

    public function getResponse(){
        return $this->response;
    }

    public function getMandatoryFields(){
        return $this->mandatoryFields;
    }
}