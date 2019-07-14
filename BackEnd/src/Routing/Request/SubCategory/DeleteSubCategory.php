<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/8/2019
 * Time: 10:24 PM
 */

namespace BackEnd\Routing\Request\SubCategory;

use BackEnd\Database\DBSubCategories\UndefinedSubCategoryID;
use BackEnd\Routing\Request\Request;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Routing\Request\MissingParametersException;

class DeleteSubCategory extends Request
{
    /** @var DBSubCategories */
    protected $subCategoriesTable;
    protected $categoryId;

    public function __construct($subCategoriesTable, $data)
    {
        $mandatoryFields = ["category_id"];
        parent::__construct($data, $mandatoryFields, "DeleteSubCategories");
        $this->subCategoriesTable = $subCategoriesTable;
    }

    public function execute()
    {
        try {
            $this->checkRequiredParameters();
            $this->subCategoriesTable->deleteSubCategory($this->categoryId);
            $this->response["STATUS"] = "OK";
        } catch (MissingParametersException | UndefinedSubCategoryID $exception) {
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    public function getSubCategoryTable()
    {
        return $this->subCategoriesTable;
    }

}