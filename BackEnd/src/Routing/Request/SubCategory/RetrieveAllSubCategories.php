<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 10:28 AM
 */

namespace BackEnd\Routing\Request\Category;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;

class RetrieveAllSubCategories extends Request
{
    protected $sessionId;
    protected $userId;

    /** @var \BackEnd\User */
    protected $user;
    /** @var DBCategories */
    protected $categoriesTable;
    /** @var DBSubCategories */
    protected $subCategoriesTable;
    /** @var DBUsers */
    protected $usersTable;

    public function __construct($subCategoriesTable, $categoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["session_id", "user_id"];
        parent::__construct($data, $mandatoryFields, "RetrieveSubCategories");
        $this->categoriesTable = $categoriesTable;
        $this->subCategoriesTable = $subCategoriesTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(){
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $categories = $this->categoriesTable->getAllCategories();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array();
            foreach($categories as $category){
                $this->response["DATA"][] = $category->asDict();
            }
        }
        catch(MissingParametersException | InvalidSessionException $exception){
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
    }

    public function getCategoriesTable(){
        return $this->categoriesTable;
    }

    public function getUsersTable(){
        return $this->usersTable;
    }

    public function getSubCategoriesTable(){
        return $this->subCategoriesTable;
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
}