<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/13/2019
 * Time: 10:29 PM
 */

namespace BackEnd\Routing\Request\SubCategory;
use BackEnd\Category;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\Request;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\SubCategory;
use BackEnd\User;

class SubCategoryCreation extends Request
{
    protected $name;
    protected $parentId;
    protected $sessionId;
    protected $userId;
    /** @var DBCategories */
    protected $categoriesTable;
    /** @var DBSubCategories */
    protected $subCategoriesTable;
    protected $category;
    /** @var User */
    protected $user;
    /** @var DBUsers */
    protected $usersTable;
    public function __construct($subCategoriesTable, $categoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name", "parent_id", "session_id", "user_id"];
        parent::__construct($data, $mandatoryFields, "SubCategoryCreation");
        $this->categoriesTable = $categoriesTable;
        $this->subCategoriesTable = $subCategoriesTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(): void{
        $this->response = [];
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $this->tryAddingSubCategory();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = $this->category;
        }
        catch(MissingParametersException | InvalidSessionException |
        \BackEnd\Database\InsertionException  $exception){
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    public function getSubCategoriesTable(){
        return $this->subCategoriesTable;
    }

    public function getCategoriesTable(){
        return $this->categoriesTable;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    /**
     * @throws InvalidSessionException
     */
    protected function tryConnectingUser(): void
    {
        $this->user->connectWithSessionID($this->usersTable, $this->sessionId, $this->userId);
        if (!$this->user->isConnected()) {
            throw new InvalidSessionException($this->requestName);
        }
    }

    /**
     * @throws \BackEnd\Database\InsertionException
     */
    protected function tryAddingSubCategory(): void
    {
        $addedDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $addedDate = $addedDate->format("Y-m-d H:i:s");
        $this->category = new SubCategory($this->name, $this->parentId, $this->userId, $addedDate);

        $this->subCategoriesTable->addSubCategory($this->category);
    }
}