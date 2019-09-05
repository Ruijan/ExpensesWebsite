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
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;

class RetrieveAllCategories extends ConnectedRequest
{
    /** @var DBCategories */
    protected $categoriesTable;

    public function __construct($categoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = [];
        parent::__construct("RetrieveCategories",$mandatoryFields, $usersTable, $user, $data);
        $this->categoriesTable = $categoriesTable;
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
}