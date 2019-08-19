<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/21/2019
 * Time: 10:35 AM
 */

namespace BackEnd\Routing\Request\Payee;


use BackEnd\Database\DBPayees\DBPayees;
use BackEnd\Database\DBPayees\UndefinedPayeeException;
use BackEnd\Routing\Request\ConnectedRequest;

class DeletePayee extends ConnectedRequest
{
    protected $payeeId;
    /** @var DBPayees */
    protected $payeesTable;

    public function __construct($payeesTable, $usersTable, $user, $data)
    {
        $mandatoryFields = ["payee_id"];
        parent::__construct("DeletePayee", $mandatoryFields, $usersTable, $user, $data);
        $this->payeesTable = $payeesTable;
    }

    public function execute()
    {
        parent::execute();
        if($this->valid){
            $this->response = [];
            try{
                $this->checkIPayeeExists();
                $this->payeesTable->deletePayee($this->payeeId);
                $this->response["STATUS"] = "OK";
            }catch(UndefinedPayeeException $exception){
                $this->buildResponseFromException($exception);
            }

            $this->response = json_encode($this->response);
        }
    }

    public function getPayeesTable(){
        return $this->payeesTable;
    }

    protected function checkIPayeeExists(): void
    {
        if (!$this->payeesTable->doesPayeeIDExist($this->payeeId)) {
            throw new UndefinedPayeeException($this->payeeId);
        }
    }


}