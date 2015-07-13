<?php

namespace IAbstract;


interface IRouter
{
    /**
     * @param $path
     * @param array $route
     * @return mixed
     */
    public function add($path, Array $route);

    /**
     * @param array $route
     * @return mixed
     */
    public function remove(Array $route);

    /**
     * @return mixed
     */
    public function resolve();

    /**
     * @param $namespace
     * @return mixed
     */
    public function setDefaultModule($namespace);

    /**
     * @param $controller
     * @return mixed
     */
    public function setDefaultController($controller);

    /**
     * @param $action
     * @return mixed
     */
    public function setDefaultAction($action);

    /**
     * @param $url
     * @param bool $permanent
     * @return mixed
     */
    public static function appRedirect($url, $permanent = false);
}