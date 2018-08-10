<?php

namespace PriceMonitorPlentyIntegration\Helper;

class StringUtils
{
    public static function getUniqueString($len)
    {
        $string = "";
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i=0;$i<$len;$i++) {
            $string.=substr($chars, rand(0,strlen($chars)), 1);
        }
                
        return $string;
    }
}