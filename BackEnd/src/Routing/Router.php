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
        $this->request->execute();
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
        $formattedRoute = $this->makeFormattedRoute();
        $path = explode('/', $formattedRoute);
        $factoryName = $path[0];
        if (!array_key_exists($factoryName, $this->requestFactories)) {
            throw new \InvalidArgumentException("Wrong path ".$factoryName);
        }
        unset($path[0]);
        $newRoute = implode('/', $path);
        $this->request = $this->requestFactories[$factoryName]->createRequest($newRoute);
    }

    public function getResponse(){
        return $this->request->getResponse();
    }

    /**
     * @return bool|mixed|string
     */
    protected function makeFormattedRoute()
    {

        $needle = "action=";
        $url = $this->serverProperties->getURI();
        if (strpos($url, $needle) > 0) {
            $formattedRoute = substr($url, strpos($url, "action=") + strlen($needle));
            return $formattedRoute;
        }
        $root = $this->serverProperties->getDocumentRoot();
        $currentFolder = str_replace('\\', '/', $this->serverProperties->getCurrentFolder());
        $current_path = str_replace('\\', '/', str_replace(
            $root . '/',
            '',
            $currentFolder));
        $formattedRoute = $this->formatRoute($url);
        $formattedRoute = str_replace($current_path . '/', '', $formattedRoute);
        $formattedRoute = substr($formattedRoute, 1);
        return $formattedRoute;
    }

}