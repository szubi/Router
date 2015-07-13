<?php

namespace Components;


class Request
{
    private $request_type;

    /**
     * @return array
     *
     * to nie jest zadanie routera a klasy Request!!!!!!!!!
     */
    public function giveGetIfExist()
    {
        $get=[];

        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $key = Helper::stringFilter($key);
                $value = Helper::stringFilter($value);
                $get[$key] = $value;
            }
        }

        return $get;
    }

    public function parse()
    {
        switch ($this->request_type) {
            case 'get':
                echo 'cos';
                var_dump($_GET);
                break;
            case 'post':
                var_dump($_POST);
                break;
        }
    }

    private function getElement($value, $type)
    {
        $value = str_replace(' ', '', $value);
        $elements = explode('|', trim($value, '|'));

        foreach ($elements as $element) {
            if ($type === strtolower($element)) {
                return true;
            }
        }

        return false;
    }

    public function checkRequest(Array $type=null)
    {
        $request_method = 'get';

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $request_method = strtolower($_SERVER['REQUEST_METHOD']);
        }

        if (count($type) == 1 && $type !== null) {
            foreach ($type as $key => $value) {
                if ('method' === $key) {

                    switch ($request_method) {
                        case 'get':

                            $this->request_type = 'get';
                            return ($this->getElement($value, 'get') === true) ? true : false;

                            break;
                        case 'post':

                            $this->request_type = 'post';
                            return ($this->getElement($value, 'post') === true) ? true : false;

                            break;
                    }

                }
            }
        } elseif ($type === null) {

            return ($request_method == 'get') ? true : false;
        }

        return false;
    }

    public static function getResponse()
    {

    }
}
