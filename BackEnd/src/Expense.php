<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 10:49 PM
 */

namespace src;


use mysql_xdevapi\Exception;

class Expense
{
    protected $data = ["id" => NULL,
        "expense_date" => NULL,
        "location" => NULL,
        "account" => NULL,
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

    public function __construct($array)
    {
        $newArray = $array;
        $diffKeys = array_diff_key($newArray, $this->data);
        if(array_intersect_key($diffKeys, $this->data) !== $diffKeys) {
            $newArray = array_change_key_case($newArray, CASE_LOWER);
            $diffKeys = array_diff_key($newArray, $this->data);
            if(array_intersect_key($diffKeys, $this->data) !== $diffKeys) {
                throw new \Exception();
            }
        }
        $this->data = array_merge($this->data, $newArray);

    }

    public function asPrintableArray(){
        $keys = array("id", "expense_date", "location", "account", "payee",
            "category", "sub_category", "amount", "currency", "state");
        return array_intersect_key($this->data, array_flip($keys));/*array(
            "id" => $this->id,
            "expense_date" => $this->date,
            "location" => $this->location,
            "payer" => $this->payer,
            "payee" => $this->payee,
            "category" => $this->category,
            "sub_category" => $this->subCategory,
            "amount" => $this->amount,
            "currency" => $this->currency,
            "state" => $this->state
        );*/
    }

    public function asArray(){
        return $this->data;
    }
}