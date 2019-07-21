<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:58 AM
 */

namespace BackEnd\Routing\Request\Payee;

use BackEnd\Database\DBTables;
use BackEnd\User;
use BackEnd\Database\Database;

class PayeeRequestFactory
{
    /** @var Database */
    private $database;

    public function __construct($database)
    {
        $this->database = $database;
    }

    public function createRequest($type)
    {
        $postArray = filter_var_array($_POST, FILTER_SANITIZE_SPECIAL_CHARS);
        switch ($type) {
            case "Create":
                return new PayeeCreation($this->database->getTableByName(DBTables::PAYEES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            case "RetrieveAll":
                return new RetrieveAllPayees($this->database->getTableByName(DBTables::PAYEES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            case "Delete":
                return new DeletePayee($this->database->getTableByName(DBTables::PAYEES),
                    $this->database->getTableByName(DBTables::USERS), new User(), $postArray);
            default:
                throw new \InvalidArgumentException("Request type: " . $type . " not found.");
        }
    }

    public function getDatabase()
    {
        return $this->database;
    }
}