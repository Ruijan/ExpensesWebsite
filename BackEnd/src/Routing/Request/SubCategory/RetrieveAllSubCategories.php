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
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;

class RetrieveAllSubCategories extends Request
{
    protected $sessionId;
    protected $userId;

    /** @var \BackEnd\User */
    protected $user;
    /** @var DBSubCategories */
    protected $subCategoriesTable;
    /** @var DBUsers */
    protected $usersTable;

    public function __construct($subCategoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["session_id", "user_id"];
        parent::__construct($data, $mandatoryFields, "RetrieveSubCategories");
        $this->subCategoriesTable = $subCategoriesTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(){
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $subCategories = $this->subCategoriesTable->getAllSubCategories();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = array();
            foreach($subCategories as $subCategory){
                $this->response["DATA"][] = $subCategory->asDict();
            }
        }
        catch(MissingParametersException | InvalidSessionException $exception){
            $this->buildResponseFromException($exception);
        }
        $this->response = json_encode($this->response);
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