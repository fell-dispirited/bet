<?php

namespace bet;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/28
 * Time: 9:27

 */
require_once 'filePool.php';
require_once 'int_pool.php';

//博彩
class Factory
{
    private static $provider = [
        'file' => filePool::class,

        ];

    public static function make($name)
    {
        return new self::$provider[$name];
    }

}