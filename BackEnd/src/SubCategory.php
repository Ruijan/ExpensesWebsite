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
    protected $subCategoryID;

    public function __construct($name, $subCategoryID, $userID, $addedDate){
        parent::__construct($name, $userID, $addedDate);
        $this->subCategoryID = $subCategoryID;
    }

    public function asDict(){
        return array(
            "id" => $this->subCategoryID,
            "name" => $this->name,
            "parent_id" => $this->categoryID,
            "user_id" => $this->userID,
            "added_date" => $this->addedDate);
    }

    public function getParentID(){
        return $this->subCategoryID;
    }

    public function setID($subCategoryID)
    {
        $this->subCategoryID = $subCategoryID;
    }

}