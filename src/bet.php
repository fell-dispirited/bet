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
    private $status = true;

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
    public function getPool($name = false)
    {
        if(is_bool($name)&&!$name)
        {
            $file = $this->prize->scanFile();
            $fp = fopen($this->prize->getCurrentPath().$file,'r');
            return json_decode(fgets($fp,999),true);
        }
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
        //清空奖池
        $fp = fopen($this->prize->getCurrentPath().'pool.txt','w');
        //清空中奖名单
        $fp2 = fopen($this->prize->getHistoryPath().$this->prize->file,'w');

        fwrite($fp,$pool);

        fclose($fp);
        fclose($fp2);

    }
    //重置奖池
    public function retPool()
    {

    }


    //抽奖
    public function draw($name)
    {

        if(!$this->status)
        {
            return ['error'=>1,'msg'=>'当前奖池未开启'];
        }
        //读取数据
        $fp = fopen($path = $this->prize->getCurrentPath().'pool.txt','r+');

        if(flock($fp,LOCK_EX)) {

            $pool = fgets($fp, 999);

            $pool = str_split(str_shuffle($pool));

            $num = array_shift($pool);

            ftruncate($fp, 0);

            rewind($fp);

            fputs($fp, implode($pool));

            flock($fp,LOCK_UN);

            fclose($fp);
        }

        if($num =='')
        {
            die('奖池已抽完');
        }

        $fp2 = fopen($history = $this->prize->getHistoryPath().$this->prize->file,'r+');

        if(flock($fp2,LOCK_EX)) {

            $history = json_decode(fgets($fp2, 999),true);

            if(is_null($history)){$history = [];}

            array_push($history,[
                'num'=> $num,
                'name'=>$name,
                'time'=>date('Y-m-d H:i:s',time()),
            ]);

            rewind($fp2);

            fwrite($fp2,json_encode($history));

            flock($fp2,LOCK_UN);

            fclose($fp2);
        }

        $fp3 = fopen($this->prize->getCurrentPath().$this->prize->file,'r');

        $data = fgets($fp3,999);


        return json_decode($data,true)[$num];

    }

    //取出奖池
    public function putPool()
    {

    }




    //开启奖池
    public function startPool()
    {
        $this->status = true;

        $this->initialize();
    }
    //关闭奖池
    public function closePool()
    {
        $this->status = false;
    }

    //获取奖池组
    public function getPoolGroup()
    {
        $data = scandir($this->prize->getPath());
        return array_map(function($value){
            return substr($value,0,strpos($value,'.'));
        },array_filter(scandir($this->prize->getPath()),function($value){
            return ($value!='.'&&$value!='..');
        }));
    }




}