<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/28
 * Time: 17:02
 */

namespace bet;


interface int_bet
{
    //重置奖池
    public function retPool();

    //抽奖
    public function draw();

    //修改奖池
    public function modPool();

    //开启奖池
    public function startPool();

    //关闭奖池
    public function closePool();

    //初始化奖池
    public function initialize();

}