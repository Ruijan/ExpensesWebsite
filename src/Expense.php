<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 12/2/2018
 * Time: 10:49 PM
 */

namespace src;


class Expense
{
    protected $id;
    protected $date;
    protected $location;
    protected $payer;
    protected $payerID;
    protected $payee;
    protected $payeeID;
    protected $category;
    protected $categoryID;
    protected $subCategory;
    protected $subCategoryID;
    protected $amount;
    protected $currency;
    protected $currencyID;
    protected $state;
    protected $stateID;

    public function __construct($array)
    {
        foreach ($array as $key => $value){
            $key = strtolower($key);
            if($key == "id"){
                $this->id = $value;
            }
            elseif($key == "expense_date"){
                $this->date = $value;
            }
            elseif($key == "location"){
                $this->location = $value;
            }
            elseif($key == "payer"){
                $this->payer = $value;
            }
            elseif($key == "payee"){
                $this->payee = $value;
            }
            elseif($key == "category"){
                $this->category = $value;
            }
            elseif($key == "sub_category"){
                $this->subCategory = $value;
            }
            elseif($key == "amount"){
                $this->amount = $value;
            }
            elseif($key == "currency"){
                $this->currency = $value;
            }
            elseif($key == "state"){
                $this->state = $value;
            }
        }
    }

    public function asPrintableArray(){
        return array(
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
        );
    }

    public function asArray(){
        return array(
            "id" => $this->id,
            "expense_date" => $this->date,
            "location" => $this->location,
            "payer" => $this->payer,
            "payerID" => $this->payerID,
            "payee" => $this->payee,
            "payeeID" => $this->payeeID,
            "category" => $this->category,
            "categoryID" => $this->categoryID,
            "sub_category" => $this->subCategory,
            "sub_categoryID" => $this->subCategoryID,
            "amount" => $this->amount,
            "currency" => $this->currency,
            "currencyID" => $this->currencyID,
            "state" => $this->state,
            "stateID" => $this->stateID
        );
    }
}