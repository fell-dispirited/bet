<?php

namespace bet;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/28
 * Time: 9:27

 */
require_once 'prize.php';
require_once 'int_bet.php';

//博彩
class bet implements int_bet
{
    //奖池人数
    private $num = 20;
    //存储方式
    private $contentType = 'file';
    //奖品
    private $prize;
    //是否开启了奖池
    private $status;

    public function __construct()
    {
        $this->prize = new prize();
    }

    //设置奖池
    public function setPool($prize,$num,$name)
    {
        $this->prize->setPool($prize,$num,$name);
    }

    //获取当前的奖池
    public function getPool($name)
    {
        return $this->prize->getByFile($name);
    }

    //初始化奖池
    public function initialize()
    {
        $data = $this->prize->getByFile($this->prize->file);

        $pool = '';

        foreach($data as $key => $val)
        {
            $pool = str_pad($pool,strlen($pool)+$val['num'],$key);
        }

        $fp = fopen($this->prize->getCurrentPath().'pool.txt','w');

        fwrite($fp,$pool);

        fclose($fp);

    }
    //重置奖池
    public function retPool()
    {

    }

    //抽奖
    public function draw()
    {
        //读取数据
        $fp = fopen($this->prize->getCurrentPath().'pool.txt','r+');

        $pool = fgets($fp,999);

        $pool = str_split(str_shuffle($pool));

        $num = array_shift($pool);

        ftruncate($fp,0);

        rewind($fp);

        fputs($fp,implode($pool));

        fclose($fp);

        return $num;

    }

    //取出奖池
    public function putPool()
    {

    }




    //开启奖池
    public function startPool()
    {
        $this->status = true;
    }
    //关闭奖池
    public function closePool()
    {
        $this->status = false;
    }


    public function modPool()
    {

    }



}