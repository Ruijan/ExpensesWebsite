<?php
/**
 * Created by PhpStorm.
 * User: MSI-GP60
 * Date: 2/2/2019
 * Time: 9:25 PM
 */

namespace BackEnd\Routing;
use BackEnd\Routing\Request\Request;
use http\Exception\InvalidArgumentException;
use BackEnd\Routing\Request\ConnectionRequestFactory;

class Router
{
    private $request;
    private $response;
    private $serverProperties;


    function __construct(ServerProperties $serverProperties)
    {
        $this->serverProperties = $serverProperties;
    }

    function resolveRoute()
    {
        $formattedRoute = $this->formatRoute($this->serverProperties->getURI());
        $path = explode('/', $formattedRoute);
        switch($path[0]){
            case "connection":
                unset($path[0]);
                $this->request = new ConnectionRequestFactory(implode('/', $path));
                break;
            default:
                throw new \InvalidArgumentException("Wrong path");
        }
        $this->request->init();
        $this->response = $this->request->getResponse();
        $this->response->execute();
    }

    private function formatRoute($route)
    {
        $result = rtrim($route, '/');
        if ($result === '')
        {
            return '/';
        }
        return $result;
    }

}