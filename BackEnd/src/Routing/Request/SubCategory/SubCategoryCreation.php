<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/13/2019
 * Time: 10:29 PM
 */

namespace BackEnd\Routing\Request\SubCategory;

use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\SubCategory;

class SubCategoryCreation extends ConnectedRequest
{
    protected $name;
    protected $parentId;
    /** @var DBCategories */
    protected $categoriesTable;
    /** @var DBSubCategories */
    protected $subCategoriesTable;
    protected $subCategory;

    public function __construct($subCategoriesTable, $categoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name", "parent_id"];
        parent::__construct("SubCategoryCreation", $mandatoryFields, $usersTable, $user, $data);
        $this->categoriesTable = $categoriesTable;
        $this->subCategoriesTable = $subCategoriesTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(): void
    {
        parent::execute();
        if ($this->valid) {
            $this->response = [];
            try {
                $this->tryAddingSubCategory();
                $this->response["STATUS"] = "OK";
                $this->response["DATA"] = $this->subCategory->asDict();
            } catch (\BackEnd\Database\InsertionException  $exception) {
                $this->valid = false;
                $this->buildResponseFromException($exception);
            }
            $this->response = json_encode($this->response);
        }
    }

    /**
     * @throws \BackEnd\Database\InsertionException
     */
    protected function tryAddingSubCategory(): void
    {
        $addedDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $addedDate = $addedDate->format("Y-m-d H:i:s");
        $this->subCategory = new SubCategory($this->name, $this->parentId, $this->userId, $addedDate);

        $this->subCategoriesTable->addSubCategory($this->subCategory);
    }

    public function getSubCategoriesTable()
    {
        return $this->subCategoriesTable;
    }

    public function getCategoriesTable()
    {
        return $this->categoriesTable;
    }
}