<?php

namespace App\MyUtil;

class MyHelper
{

    static public function getHost($url)
    {
        try {
            $parts = explode('.', $url);
            if (count($parts) === 2) return $url;
            $rev = array_reverse($parts);
            return $rev[1] . '.' . $rev[0];
        } catch (\Throwable $th) {
            return null;
        }
    }

    static public function urlSanitize($url)
    {
        $newUrl = parse_url(strtolower($url));
        $host  = self::getHost($newUrl['host']);
        if (is_null($host) || empty($host)) {
            throw new \Exception("Bad URL");
        }
        $output =  $newUrl['scheme'] . "://www." . $host;
        return $output;
    }
}
