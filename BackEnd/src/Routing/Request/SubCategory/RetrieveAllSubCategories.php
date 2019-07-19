<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 10:28 AM
 */

namespace BackEnd\Routing\Request\SubCategory;

use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;

class RetrieveAllSubCategories extends ConnectedRequest
{
    /** @var DBSubCategories */
    protected $subCategoriesTable;

    public function __construct($subCategoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = [];
        parent::__construct("RetrieveSubCategories", $mandatoryFields, $usersTable, $user, $data);
        $this->subCategoriesTable = $subCategoriesTable;
    }

    public function execute()
    {
        parent::execute();
        if ($this->valid) {
            $this->response = [];
            $subCategories = $this->subCategoriesTable->getAllSubCategories();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array();
            foreach ($subCategories as $subCategory) {
                $this->response["DATA"][] = $subCategory->asDict();
            }
            $this->response = json_encode($this->response);
        }
    }


    public function getSubCategoriesTable()
    {
        return $this->subCategoriesTable;
    }
}