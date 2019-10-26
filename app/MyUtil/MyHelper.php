<?php

namespace App\MyUtil;

class MyHelper
{
    static public function urlSanitize($url)
    {
        $newUrl = parse_url($url);
        return $newUrl['scheme'] . "://" . $newUrl['host'];
    }
}
