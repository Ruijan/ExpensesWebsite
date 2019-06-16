<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 3:19 PM
 */

namespace BackEnd;


class Category
{
    protected $name;
    protected $userID;
    protected $addedDate;

    public function __construct($name, $userID, $addedDate){
        $this->name = $name;
        $this->userID = $userID;
        $this->addedDate = $addedDate;
    }

    public function asDict(){
        return array("name" => $this->name,
            "user_id" => $this->userID,
            "added_date" => $this->addedDate);
    }

    public function getName(){
        return $this->name;
    }

    public function getUserID(){
        return $this->userID;
    }

    public function getAddedDate(){
        return $this->addedDate;
    }
}