<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/18/2019
 * Time: 11:01 PM
 */

namespace BackEnd;


class SubCategory
{
    protected $name;
    protected $userID;
    protected $addedDate;
    protected $parentID;

    public function __construct($name, $parentID, $userID, $addedDate){
        $this->name = $name;
        $this->parentID = $parentID;
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