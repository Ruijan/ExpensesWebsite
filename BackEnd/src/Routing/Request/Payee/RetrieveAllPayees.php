<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 12:00 PM
 */

namespace BackEnd\Routing\Request\Payee;


use BackEnd\Database\DBPayees\DBPayees;
use BackEnd\Routing\Request\ConnectedRequest;

class RetrieveAllPayees extends ConnectedRequest
{
    /** @var DBPayees */
    protected $payeesTable;

    public function __construct($payeesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = [];
        parent::__construct("RetrievePayees", $mandatoryFields, $usersTable, $user, $data);
        $this->payeesTable = $payeesTable;
    }

    public function execute()
    {
        parent::execute();
        if ($this->valid) {
            $this->response = [];
            $payees = $this->payeesTable->getAllPayees();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = $payees;
            $this->response = json_encode($this->response);
        }
    }

    public function getPayeesTable()
    {
        return $this->payeesTable;
    }
}