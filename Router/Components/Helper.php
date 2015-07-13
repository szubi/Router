<?php

namespace Components;


class Helper
{
    public static function stringFilter($string)
    {
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        $string = filter_var($string, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        return $string;
    }
}
