<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/8/2019
 * Time: 10:24 PM
 */

namespace BackEnd\Routing\Request\SubCategory;

use BackEnd\Database\DBSubCategories\UndefinedSubCategoryID;
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\Database\DBSubCategories\DBSubCategories;

class DeleteSubCategory extends ConnectedRequest
{
    /** @var DBSubCategories */
    protected $subCategoriesTable;
    protected $categoryId;

    public function __construct($subCategoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["category_id"];
        parent::__construct("DeleteSubCategories", $mandatoryFields, $usersTable, $user, $data);
        $this->subCategoriesTable = $subCategoriesTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute()
    {
        parent::execute();
        if ($this->valid) {
            $this->response = [];
            try {
                $this->subCategoriesTable->deleteSubCategory($this->categoryId);
                $this->response["STATUS"] = "OK";
            } catch (UndefinedSubCategoryID $exception) {
                $this->valid = false;
                $this->buildResponseFromException($exception);
            }
            $this->response = json_encode($this->response);
        }
    }

    public function getSubCategoryTable()
    {
        return $this->subCategoriesTable;
    }

}