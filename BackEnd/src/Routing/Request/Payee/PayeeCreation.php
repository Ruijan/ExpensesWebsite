<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:06 AM
 */
namespace BackEnd\Routing\Request\Payee;
use BackEnd\Database\DBPayees\DBPayees;
use BackEnd\Database\InsertionException;
use BackEnd\Routing\Request\ConnectedRequest;

class PayeeCreation extends ConnectedRequest
{
    protected $name;
    /** @var DBPayees */
    protected $payeesTable;

    public function __construct($payeesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["name"];
        parent::__construct("PayeeCreation",
            $mandatoryFields,
            $usersTable,
            $user,
            $data);
        $this->payeesTable = $payeesTable;
    }

    public function execute(): void{
        parent::execute();
        if($this->valid){
            $this->response = [];
            try{
                $payeeID = $this->payeesTable->addPayee($this->name);
                $this->response["STATUS"] = "OK";
                $this->response["DATA"] = array("ID" => $payeeID,
                    "NAME" => $this->name);
            }
            catch(InsertionException $exception){
                $this->buildResponseFromException($exception);
            }
            $this->response = json_encode($this->response);
        }
    }

    public function getPayeesTable(){
        return $this->payeesTable;
    }
}