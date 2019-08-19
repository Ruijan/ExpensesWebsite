<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:58 AM
 */

namespace BackEnd\Routing\Request\Payee;

use BackEnd\Database\DBTables;
use BackEnd\Routing\Request\RequestFactory;
use BackEnd\User;

class PayeeRequestFactory extends RequestFactory
{
    public function createRequest($type, $data)
    {
        switch ($type) {
            case "Create":
                return new PayeeCreation($this->database->getTableByName(DBTables::PAYEES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "RetrieveAll":
                return new RetrieveAllPayees($this->database->getTableByName(DBTables::PAYEES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            case "Delete":
                return new DeletePayee($this->database->getTableByName(DBTables::PAYEES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $data);
            default:
                throw new \InvalidArgumentException("Request type: " . $type . " not found.");
        }
    }
}