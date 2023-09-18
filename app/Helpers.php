<?php

namespace App;

use Illuminate\Support\Facades\Redis;

/**
 *
 */
class Helpers {

    public static function getArrObjIndex($needle,$hayStack,$key): bool|int|string
    {
        $array = ( $hayStack ? $hayStack : []);
        return array_search($needle, array_column($array, $key));
    }

    public static function updateArrObj($needle, $hayStack, $key, $newValue): array
    {
        $array = ( $hayStack ? $hayStack : []);
        $index = array_search($needle, array_column($array, $key));

        if($index != -1) {
            $array[$index] = $newValue;
        }
        return $array;
    }

    public static function getArrObj($needle, $hayStack, $key): object|bool
    {
        $array = ( $hayStack ? $hayStack : []);
        $index = array_search($needle, array_column($array, $key));
        return $array[$index];
    }

    public static function isValidDate($date): mixed
    {
        $timestamp = strtotime($date);
        return $timestamp ? $date  : null ;
    }

    public static function castToClass($class, $object): mixed
    {
        return unserialize(preg_replace('/^O:\d+:"[^"]++"/', 'O:' . strlen($class) . ':"' . $class . '"', serialize($object)));
    }

    public static function getRedisDataArr($key): mixed
    {
        $redis = json_decode(Redis::get($key));
        return ($redis) ? : [];
    }

    public static function setRedisDataArr($key, $data)
    {
        return Redis::set($key, json_encode($data));
    }


}
