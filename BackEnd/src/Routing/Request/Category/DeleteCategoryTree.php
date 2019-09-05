<?php
/**
 * Created by PhpStorm.
 * User: Julien
 * Date: 8/24/2019
 * Time: 2:59 PM
 */

namespace BackEnd\Routing\Request\Category;

use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBCategories\UndefinedCategoryID;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Routing\Request\ConnectedRequest;

class DeleteCategoryTree extends ConnectedRequest
{
    /** @var DBCategories*/
    protected $categoriesTable;
    /** @var DBSubCategories */
    protected $subCategoriesTable;
    protected $categoryId;
    public function __construct($categoriesTable, $subCategoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["category_id"];
        parent::__construct("DeleteCategoryTree", $mandatoryFields, $usersTable, $user, $data);
        $this->categoriesTable = $categoriesTable;
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
                $this->categoriesTable->deleteCategory($this->categoryId);
                $this->subCategoriesTable->deleteSubCategoryFromParentID($this->categoryId);
                $this->response["STATUS"] = "OK";
            } catch (UndefinedCategoryID $exception) {
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
    public function getCategoryTable()
    {
        return $this->categoriesTable;
    }
}