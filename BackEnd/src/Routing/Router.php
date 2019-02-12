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
    private $requestFactories;

    function __construct($serverProperties, array $requestFactories)
    {
        $this->serverProperties = $serverProperties;
        $this->requestFactories = $requestFactories;
    }

    function resolveRoute()
    {
        $this->generateRequest();
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

    public function getServerProperties(){
        return $this->serverProperties;
    }

    public function getRequestFactories(){
        return $this->requestFactories;
    }

    protected function generateRequest(): void
    {
        $formattedRoute = $this->formatRoute($this->serverProperties->getURI());
        $path = explode('/', $formattedRoute);
        $factoryName = $path[0];
        if (!array_key_exists($factoryName, $this->requestFactories)) {
            throw new \InvalidArgumentException("Wrong path");
        }
        unset($path[0]);
        $newRoute = implode('/', $path);
        $this->request = $this->requestFactories[$factoryName]->createRequest($newRoute);
    }

}