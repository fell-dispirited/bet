<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/28
 * Time: 17:02
 */

namespace bet;


interface int_pool
{
    //重置奖池
    public function retPool();

    //设置奖池
    public function setPool($prize,$num,$name);

    //抽奖
    public function draw($name);

    //获取奖池
    public function getPool($name);

    //关闭奖池
    public function closePool();

    //初始化奖池
    public function initialize();

    //获取奖池组
    public function getPoolGroup();

}