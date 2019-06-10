<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 1/25/2019
 * Time: 9:25 PM
 */

namespace BackEnd\Account;


class Account
{
    protected $data = ["id" => NULL,
        "name" => NULL,
        "current_amount" => 0,
        "currency" => NULL,
        "currency_id" => NULL,
        "added_date" => "",
        "user" => NULL,
        "user_id" => NULL
    ];
    protected $mandatoryFields = ["user_id", "name", "currency_id", "current_amount"];
    protected $format = ["id" => "integer",
        "name" => NULL,
        "current_amount" => "float",
        "currency" => NULL,
        "currency_id" => "integer",
        "added_date" => NULL,
        "user" => NULL,
        "user_id" => "integer"];
    private $expenses = [];

    public function __construct($array)
    {
        $this->fillDataWithArray($array);
        $this->checkData();
        $this->formatData();
    }

    /**
     * @param $array
     * @throws \Exception
     */
    protected function fillDataWithArray($array): void
    {
        $newArray = $array;
        $diffKeys = array_diff_key($newArray, $this->data);
        if (array_intersect_key($diffKeys, $this->data) !== $diffKeys) {
            $newArray = array_change_key_case($newArray, CASE_LOWER);
            $diffKeys = array_diff_key($newArray, $this->data);
            if (array_intersect_key($diffKeys, $this->data) !== $diffKeys) {
                throw new \Exception();
            }
        }
        $this->data = array_merge($this->data, $newArray);
    }

    protected function checkData(): void
    {
        $missingParameters = [];
        foreach ($this->mandatoryFields as $field) {
            if ($this->data[$field] == NULL) {
                $missingParameters[] = $field;
            }
        }
        if (sizeof($missingParameters) > 0) {
            throw new MissingParametersException($missingParameters);
        }
    }

    protected function formatData(): void
    {
        foreach ($this->data as $key => $value) {
            if ($this->format[$key] == "integer") {
                $this->data[$key] = (int)$value;
            } elseif ($this->format[$key] == "float") {
                $this->data[$key] = (float)$value;
            }
        }
    }

    public function getName()
    {
        return $this->data["name"];
    }

    public function getCurrentAmount()
    {
        return $this->data["current_amount"];
    }

    public function getTableID()
    {
        return $this->data["id"];
    }

    public function getCurrency()
    {
        return $this->data["currency"];
    }

    public function getCurrencyID()
    {
        return $this->data["currency_id"];
    }

    public function getUser()
    {
        return $this->data["user"];
    }

    public function getUserID()
    {
        return $this->data["user_id"];
    }

    public function asDict()
    {
        return $this->data;
    }

    public function setID($accountID)
    {
        $this->data["id"] = $accountID;
    }

    public function asPrintableArray()
    {
        $keys = array("name", "currency", "current_amount", "user");
        return array_intersect_key($this->data, array_flip($keys));
    }

    public function loadExpenses($expensesTable)
    {
        $this->expenses = $expensesTable->getExpensesForAccountID($this->data["id"]);
    }

    public function getExpenses()
    {
        return $this->expenses;
    }
}