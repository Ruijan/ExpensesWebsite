<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 10:49 PM
 */

namespace BackEnd;


use mysql_xdevapi\Exception;

class Expense
{
    protected $data = ["id" => NULL,
        "expense_date" => NULL,
        "location" => NULL,
        "account_id" => NULL,
        "payee" => NULL,
        "payee_id" => NULL,
        "category" => NULL,
        "category_id" => NULL,
        "sub_category" => NULL,
        "sub_category_id" => NULL,
        "amount" => NULL,
        "currency" => NULL,
        "currency_id" => NULL,
        "state" => NULL,
        "state_id" => NULL
        ];
    protected $format = ["id" => "integer",
        "expense_date" => NULL,
        "location" => NULL,
        "account_id" => "integer",
        "payee" => NULL,
        "payee_id" => "integer",
        "category" => NULL,
        "category_id" => "integer",
        "sub_category" => NULL,
        "sub_category_id" => "integer",
        "amount" => "float",
        "currency" => NULL,
        "currency_id" => "integer",
        "state" => NULL,
        "state_id" => "integer"];

    public function __construct($array)
    {
        $newArray = $array;
        $diffKeys = array_diff_key($newArray, $this->data);
        if(array_intersect_key($diffKeys, $this->data) !== $diffKeys) {
            $newArray = array_change_key_case($newArray, CASE_LOWER);
            $diffKeys = array_diff_key($newArray, $this->data);
            if(array_intersect_key($diffKeys, $this->data) !== $diffKeys) {
                print_r($diffKeys);
                throw new \Exception("Unrecognized keys: ".implode(", ", array_keys (array_diff_key ($diffKeys, $this->data))));
            }
        }
        $this->data = array_merge($this->data, $newArray);
        $this->formatData();
    }

    public function asPrintableArray(){
        $keys = array("id", "expense_date", "location", "account", "payee",
            "category", "sub_category", "amount", "currency", "state");
        return array_intersect_key($this->data, array_flip($keys));
    }

    public function asArray(){
        return $this->data;
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
}