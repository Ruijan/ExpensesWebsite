<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 6/16/2019
 * Time: 10:28 AM
 */

namespace BackEnd\Routing\Request\Category;
use BackEnd\Database\DBCategories\DBCategories;
use BackEnd\Database\DBSubCategories\DBSubCategories;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\ConnectedRequest;
use BackEnd\Routing\Request\MissingParametersException;
use BackEnd\Routing\Request\Connection\InvalidSessionException;
use BackEnd\Routing\Request\Request;

class RetrieveAllTree extends ConnectedRequest
{

    /** @var DBCategories */
    protected $categoriesTable;
    /** @var DBSubCategories */
    protected $subCategoriesTable;

    public function __construct($categoriesTable, $subCategoriesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = [];
        parent::__construct("RetrieveCategories",$mandatoryFields,$usersTable, $user,$data);
        $this->categoriesTable = $categoriesTable;
        $this->subCategoriesTable = $subCategoriesTable;
    }

    public function execute(){
        try{
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $categories = $this->categoriesTable->getAllCategories();
            $subCategories = $this->subCategoriesTable->getAllSubCategories();
            $this->response["STATUS"] = "OK";
            $this->response["DATA"] = ["categories" => array(),
                "sub_categories" => array()];
            foreach($categories as $category){
                $this->response["DATA"]["categories"][] = $category->asDict();
            }
            foreach($subCategories as $subCategory){
                $this->response["DATA"]["sub_categories"][] = $subCategory->asDict();
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

    public function getSubCategoriesTable(){
        return $this->subCategoriesTable;
    }
}