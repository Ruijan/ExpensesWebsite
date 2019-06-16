<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/13/2019
 * Time: 10:29 PM
 */

namespace BackEnd\Routing\Request\Category;
use BackEnd\Category;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\Request;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\User;

class CategoryCreation extends Request
{
    protected $name;
    protected $sessionId;
    protected $userId;
    /** @var DBCategories */
    protected $categoriesTable;
    protected $category;
    /** @var User */
    protected $user;
    /** @var DBUsers */
    protected $usersTable;
    public function __construct($categoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name", "session_id", "user_id"];
        parent::__construct($data, $mandatoryFields, "CategoryCreation");
        $this->categoriesTable = $categoriesTable;
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute(): void{
        $this->response = [];
        try{
            $this->checkRequiredParameters("CategoryCreation");
            $this->tryConnectingUser();
            $this->tryAddingCategory();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = $this->category;
        }
        catch(MissingParametersException | InvalidSessionException |
        \BackEnd\Database\InsertionException | \Exception $exception){
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

    /**
     * @throws InvalidSessionException
     */
    protected function tryConnectingUser(): void
    {
        $this->user->connectWithSessionID($this->usersTable, $this->sessionId, $this->userId);
        if (!$this->user->isConnected()) {
            throw new InvalidSessionException("CategoryCreation");
        }
    }

    /**
     * @throws \BackEnd\Database\InsertionException
     */
    protected function tryAddingCategory(): void
    {
        $addedDate = new \DateTime("now", new \DateTimeZone("UTC"));
        $addedDate = $addedDate->format("Y-m-d H:i:s");
        $this->category = new Category($this->name, $this->userId, $addedDate);

        $this->categoriesTable->addCategory($this->category);
    }
}