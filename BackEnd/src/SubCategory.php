<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/18/2019
 * Time: 11:01 PM
 */

namespace BackEnd;


class SubCategory extends Category
{
    protected $parentCategoryID;

    public function __construct($name, $parentCategoryID, $userID, $addedDate){
        parent::__construct($name, $userID, $addedDate);
        $this->parentCategoryID = $parentCategoryID;
    }

    public function asDict(){
        return array(
            "id" => $this->categoryID,
            "name" => $this->name,
            "parent_id" => $this->parentCategoryID,
            "user_id" => $this->userID,
            "added_date" => $this->addedDate);
    }

    public function getParentID(){
        return $this->parentCategoryID;
    }
}