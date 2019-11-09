<?php

namespace App\MyUtil;

class MyHelper
{

    static public function parseURL($url)
    {
        return parse_url(strtolower($url));
    }

    static public function getScheme($url)
    {
        try {
            return self::parseURL($url)['scheme'];
        } catch (\Throwable $th) {
            throw new \Exception("Bad Url scheme");
        }
    }

    static public function getHost($url)
    {
        try {
            $host = self::parseURL($url)['host'];
            $parts = explode('.', $host);
            if (count($parts) === 2) return $host;
            $rev = array_reverse($parts);
            return $rev[1] . '.' . $rev[0];
        } catch (\Throwable $th) {
            throw new \Exception("Bad Url host");
        }
    }

    static public function getFullDoamin($url)
    {
        return self::getScheme($url) . "://www." . self::getHost($url);
    }
}
