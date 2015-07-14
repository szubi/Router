<?php

namespace IAbstract;


use Components\Url;
use Components\Request;

/**
 *
 * @author Wojciech Szuba
 * @contact: w.szubi@gmail.com
 * @version 1.0.0
 *
 */
abstract class AbstractRouter implements RouterInterface
{
    /**
     * @var array $routes
     * @description it stores defined routes in the system
     */
    protected $routes = [];

    /**
     * @var string $module
     * @description it stores module name -> namespace
     */
    protected $module;

    /**
     * @var string $controller
     * @description it stores controller name
     */
    protected $controller;

    /**
     * @var string $action
     * @description it stores controllers action(method) name
     */
    protected $action;

    /**
     * @var array
     * @description array to stores actions parameters
     */
    protected $params = [];

    /**
     * @var array
     * @description $regex stores regex to validate routes
     */
    protected $regex = [];

    /**
     * @var array
     * @description $regex_keys stores $regex keys for looking defined address
     */
    protected $regex_keys = [];

    /**
     * @var Request
     * @description variable to stores Request object
     */
    protected $request;

    /**
     * @var Url
     * @description it stores Url Object
     */
    protected static $url;

    /**
     * @constructor
     * @param Url $url
     * @param Request $request
     * @description it causes setters method for initialize three private filed:
     *              $url
     *              $request
     *              $regex_keys
     */
    public function __construct(Url $url, Request $request)
    {
        $this->setUrl($url);
        $this->setRequest($request);
        $this->setRegexKeys();
    }

    /**
     * @method searchAddress
     * @param array $routes
     * @param array $path_info
     * @description Function looks address which pass to current website address
     *
     *              it uses $regex's values to check variable's value from address
     *
     *              if there is a match address -> function returns true
     *              if there is not a match address -> function returns false
     * @return bool
     */
    protected function searchAddress(Array $routes, Array $path_info)
    {
        $i=0;

        $count_routes = count($routes);
        $count_path_info = count($path_info);

        foreach($path_info as $path_key => $path_value) {

            if (!isset($routes[$i]) || $count_routes > $count_path_info) {
                return false;
            }

            if (preg_match("#(^[:]{1}[a-zA-Z])#", $routes[$i])) {
                $is_exist = false;
                foreach ($this->regex_keys as $key) {
                    if ($key == $routes[$i]) {
                        if (preg_match($this->regex[$key], $path_info[$path_key])) {
                            $is_exist = true;
                            break;
                        }
                    }
                }

                if (false === $is_exist) {
                    return false;
                }
            } else {

                if (isset($path_info[$path_key])) {
                    if ($path_info[$path_key] != $routes[$i]) {
                        return false;
                    }
                }
            }
            $i++;
        }

        return true;
    }

    /**
     * @method routeArray
     * @param Url $url
     * @description
     * @return array|string
     * @throws \Exception -> if address is empty
     */
    protected function routeArray(Url $url)
    {
        $url->divideAddress($url->getAddressElements());
        return $url->getActions();
    }

    /**
     * @method setUrl
     * @param Url $url
     * @description it initializes static $url variable by Url object
     */
    protected function setUrl(Url $url)
    {
        self::$url = $url;
    }

    /**
     * @method setRequest
     * @param Request $request
     * @description it initializes $request variable by Request object
     */
    protected function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @method setRegexKeys
     * @arguments none
     * @description method gets keys(elements names) from $regex array
     *              it is using in @method searchAddress
     */
    protected function setRegexKeys()
    {
        $this->regex_keys = array_keys($this->regex);
    }
}
