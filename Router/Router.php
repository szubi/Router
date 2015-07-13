<?php

namespace Router;


use Components\Url;
use Components\Helper;
use Components\Request;
use IAbstract\AbstractRouter;


class Router extends AbstractRouter
{
    protected $routes = [];

    protected $module;
    protected $controller;
    protected $action;
    protected $params = [];

    private $default_module;
    private $default_controller;
    private $default_action;

    protected $regex = [
        ':module' => '#(^[-a-zA-Z]*)$#',
        ':controller' => '#(^[a-zA-Z]*)$#',
        ':action' => '#(^[-a-zA-Z]*)$#',
        ':name' => '#(^[-a-zA-Z]*)$#',
        ':param' => '#(^[0-9]*)$#'
    ];

    protected $regex_keys = [];

    protected $path_info = [];

    public function __construct()
    {
        $this->module = '';
        $this->default_module = '';
        $this->default_controller = '';
        $this->default_action = '';

        parent::__construct(new Url(), new Request());
    }

    private function checkPath($path)
    {
        $current_path = '';

        if (preg_match('#(^[-:\/a-zA-Z]*)$#', $path)) {
            $current_path = $path;
        }

        return $current_path;
    }

    private function checkRoute(Array $route)
    {
        $current_route = [];

        if (isset($route['controller']) && isset($route['action'])) {

            if (isset($route['module'])) {
                if (preg_match('#(^[\a-zA-Z]*)$#', $route['module']) || preg_match('#([0-9])$#', $route['module'])) {
                    $current_route['module'] = $route['module'];
                }

                unset($route['module']);
            }

            if (preg_match('#(^[a-zA-Z]*)$#', $route['controller']) || preg_match('#([0-9])$#', $route['controller'])) {
                $current_route['controller'] = $route['controller'];

                unset($route['controller']);
            }

            if (preg_match('#(^[a-zA-Z]*)$#', $route['action']) || preg_match('#([0-9])$#', $route['action'])) {
                $current_route['action'] = $route['action'];

                unset($route['action']);
            }

            if (!empty($route) && isset($current_route['controller']) && isset($current_route['action'])) {
                foreach ($route as $key => $value) {

                    switch(gettype($value)) {
                        case 'integer':
                            $value = intval($value);
                            break;
                        default:
                            $value = filter_var($value, FILTER_SANITIZE_STRING);
                            break;
                    }

                    $key = filter_var($key, FILTER_SANITIZE_STRING);

                    $current_route[$key] = $value;
                }
            }
        }

        if (empty($current_route['module']) && empty($current_route['controller']) && empty($current_route['action'])) {
            $current_route['module'] = $this->default_module;
            $current_route['controller'] = $this->default_controller;
            $current_route['action'] = $this->default_action;
        }

        return $current_route;
    }

    private function checkMethod(Array $method)
    {
        $m['method'] = '';

        if (isset($method['method'])) {
            $method = str_replace(' ', '', $method['method']);
            preg_match('#(^[|a-zA-Z]*)$#', $method, $type);

            if (isset($type[0])) {
                $m['method'] = $type[0];
            }
        }

        return $m;
    }

    public function add($path, Array $route, Array $method=null)
    {
        $combine = [];

        $path = $this->checkPath($path);
        $route = $this->checkRoute($route);
        $method = $this->checkMethod($method);

        if (!empty($path) && !empty($route)) {
            $combine['path'] = $path;
            $combine['route_data'] = $route;
            $combine['request_type'] = $method;
        } else {
            $combine['path'] = '';
            $combine['route_data'] = '';
            $combine['request_type'] = '';
        }

        $this->routes[] = $combine;
    }

    public function remove(Array $route)
    {
        foreach ($this->routes as $i => $stored) {
            if ($stored == $route) {
                unset($this->routes[$i]);
            }
        }
    }

    private function complementData($path, $defined_path, $route_data)
    {
        $data = [];

        $i=0;
        $index=1;

        foreach ($path as $path_key => $path_value) {
            if (preg_match("#(^[:{1}][a-zA-Z]+)#", $defined_path[$i])) {

                foreach ($route_data as $key => $value) {
                    if ($route_data[$key] == $index) {
                        $route_data[$key] = $path[$i];
                        $data = $route_data;
                    }
                }
                $index++;
            }
            $i++;
        }

        return $data;
    }

    private function setFinalData($route_data)
    {
        $action_array = [];

        $action_array['module'] = $route_data['module'];
        $action_array['controller'] = $route_data['controller'];
        $action_array['action'] = $route_data['action'];

        unset($route_data['module']);
        unset($route_data['controller']);
        unset($route_data['action']);

        if (!empty($route_data)) {
            foreach ($route_data as $key => $value) {
                $action_array['params'][$key] = $value;
            }
        }

        return $action_array;
    }

    public function resolve()
    {
        $action_array = [];

        $url = $this->routeArray(self::$url);
        $request = $this->request->giveGetIfExist();
        $path = array_filter(array_merge($url, $request));

        foreach ($this->routes as $element) {

            $defined_path = explode('/', trim($element['path'], '/'));

            if (true === $this->searchAddress($defined_path, $path) && $this->request->checkRequest($element['request_type'])) {

                $route_data = $this->complementData($path, $defined_path, $element['route_data']);
                $action_array = $this->setFinalData($route_data);
            }
        }

        return $action_array;
    }

    public function setDefaultModule($namespace)
    {
        $this->default_module = Helper::stringFilter($namespace);
    }

    public function setDefaultController($controller)
    {
        $this->default_controller = Helper::stringFilter($controller);
    }

    public function setDefaultAction($action)
    {
        $this->default_action = Helper::stringFilter($action);
    }

    public static function appRedirect($url, $permanent = false)
    {
        $current_url = self::$url->getAddressElements();
        $path_info = self::$url->getPath();

        $path = '';
        foreach ($current_url as $element) {
            $path .= $element;
        }

        $url = Helper::stringFilter($url);
        $url = str_replace('http://', '', $url);
        $url = str_replace('www.', '', $url);
        $url = str_replace($_SERVER['SERVER_NAME'], '', $url);

        $redirect_path = str_replace($path_info, '', $path);
        $redirect_path = str_replace('?', '', $redirect_path);
        $redirect_path = str_replace($_SERVER['QUERY_STRING'], '', $redirect_path);
        $redirect_path = 'http://'.$redirect_path.$url;

        $current_path = 'http://'.$path;

        if ($redirect_path != $current_path) {
            header('Location: ' . $redirect_path, true, (true === $permanent) ? 301 : 302);
        }
    }

    public function parseRequest($type, $action=null)
    {
        $this->request->parse($type, $action);
    }
}
