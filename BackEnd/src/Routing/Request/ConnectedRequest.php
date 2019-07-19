<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 7/18/2019
 * Time: 10:19 PM
 */

namespace BackEnd\Routing\Request;
use BackEnd\User;
use BackEnd\Database\DBUsers\DBUsers;
use BackEnd\Routing\Request\Connection\InvalidSessionException;

abstract class ConnectedRequest extends Request
{
    /** @var User */
    protected $user;
    /** @var DBUsers */
    protected $usersTable;
    protected $sessionId;
    protected $userId;

    public function __construct($name, $mandatoryFields, $usersTable, $user, $data)
    {
        $mandatoryFields = array_merge($mandatoryFields, ["session_id", "user_id"]);
        parent::__construct($data, $mandatoryFields, $name);
        $this->usersTable = $usersTable;
        $this->user = $user;
    }

    public function execute()
    {
        try {
            $this->checkRequiredParameters();
            $this->tryConnectingUser();
            $this->response["STATUS"] = "OK";
        } catch (MissingParametersException | InvalidSessionException $exception) {
            $this->buildResponseFromException($exception);
            $this->valid = false;
        }
        $this->response = json_encode($this->response);
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

    public function getUsersTable(){
        return $this->usersTable;
    }
}