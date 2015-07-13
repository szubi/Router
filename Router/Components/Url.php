<?php

namespace Components;


class Url
{
    private $feedback = [];

    public function getActions()
    {
        return !empty($this->feedback) ? $this->feedback : '';
    }

    public function divideAddress(array $address)
    {
        $path='';
        if (!empty($address)) {
            foreach ($address as $element) {
                $path .= $element;
            }

            $path_info = $this->getPath();

            if (!empty($path)) {
                $elements = str_replace($path, '', $path_info);

                if (!empty($elements)) {
                    $this->feedback = explode('/',trim($elements, '/'));
                } else {
                    $this->feedback = [];
                }

            }
        } else {
            throw new \Exception('No address to convert!');
        }
    }

    public function getAddressElements()
    {
        $address[] = filter_var($_SERVER['SERVER_NAME'], FILTER_SANITIZE_STRING);
        $address[] = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_STRING);

        return !empty($address) ? $address : '';
    }

    public function getPath()
    {
        if (!empty($_SERVER['PATH_INFO'])) {
            $path_info = filter_var($_SERVER['PATH_INFO'], FILTER_SANITIZE_STRING);
        }

        return !empty($path_info) ? $path_info : '';
    }
}
