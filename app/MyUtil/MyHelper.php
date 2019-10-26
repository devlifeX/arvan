<?php

namespace App\MyUtil;

class MyHelper
{

    static public function urlSanitize($url)
    {
        $newUrl = parse_url(strtolower($url));
        $getHost = function ($url) {
            $parts = explode('.', $url);
            if (count($parts) === 2) return $url;
            $rev = array_reverse($parts);
            return $rev[1] . '.' . $rev[0];
        };
        $output =  $newUrl['scheme'] . "://www." .  $getHost($newUrl['host']);
        return $output;
    }
}
