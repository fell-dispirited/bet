<?php

namespace bet;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/28
 * Time: 9:30
 */

//奖池
class filePool implements int_pool
{
    //奖品内容
    private $content=
        [
        [
            'level'=>'一等奖',
            'description'=>'笔记本电脑',
            'num' => '1',
        ],
        [
            'level'=>'二等奖',
            'description'=>'笔记本',
            'num' => '2',
        ]
    ];
//    //奖品定义名称
//    public $name = '30';
//    //数量名称
//    public $num = 30;
    //文件保存目录
    private $path;
    //当前文件
    private $currentPath;

    public $file;

    public $status;

    public $num;
    //中奖信息保存页面
    private $hitoryPath;

    public function __construct()
    {
        $this->path = __DIR__ . '/file/drawGallery/';
        $this->currentPath = __DIR__ . '/file/current/';
        $this->hitoryPath = __DIR__ . '/file/drawHistory/';
        $this->file = is_null($this->scanFile())?'xx.jpg':$this->scanFile();
    }

    //设置奖池
    public function setPool($prize,$num,$name)
    {
        $this->file = $name.'.txt';
//        $this->content = $prize;
        $this->num = $num;
        //存储奖池
        $this->saveByFile();
        $this->copy($this->file);
        if(!is_null($file = $this->scanFile($this->file))) {
            unlink($this->currentPath.$file);
        }
    }

    //获取奖池
    public function getPool($name)
    {
        if(is_bool($name)&&!$name)
        {
            $file = $this->scanFile();
            $fp = fopen($this->getCurrentPath().$file,'r');
            return json_decode(fgets($fp,999),true);
        }
        return $this->getByFile($name);

    }

    //初始化奖池
    public function initialize()
    {
        $data = $this->getByFile($this->file);

        $pool = '';

        foreach($data as $key => $val)
        {
            $pool = str_pad($pool,strlen($pool)+$val['num'],$key);
        }
        //清空奖池
        $fp = fopen($this->getCurrentPath().'pool.txt','w');
        //清空中奖名单
        $fp2 = fopen($this->getHistoryPath().$this->file,'w');

        fwrite($fp,$pool);

        fclose($fp);

        fclose($fp2);

    }

    public function closePool()
    {
    }

    public function retPool()
    {
    }

    //获取奖池组
    public function getPoolGroup()
    {
        $data = scandir($this->getPath());
        return array_map(function($value){
            return substr($value,0,strpos($value,'.'));
        },array_filter(scandir($this->getPath()),function($value){
            return ($value!='.'&&$value!='..');
        }));
    }

    //抽奖
    public function draw($name)
    {

        if(!$this->status)
        {
            return ['error'=>1,'msg'=>'当前奖池未开启'];
        }
        //读取数据
        $fp = fopen($path = $this->getCurrentPath().'pool.txt','r+');

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

        $fp2 = fopen($history = $this->getHistoryPath().$this->file,'r+');

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

        $fp3 = fopen($this->getCurrentPath().$this->file,'r');

        $data = fgets($fp3,999);


        return json_decode($data,true)[$num];

    }


    //遍历文件
    public function scanFile($fileName = null)
    {
        $files = scandir($this->currentPath);

        foreach($files as $file)
        {
            if($file!='.'&&$file!='..'&&$file!='pool.txt'&&$file!=$fileName)
            {
                return $file;
            }
        }
        return null;
    }



    //用文件存储
    public function saveByFile()
    {
        //json格式存储
        $data = json_encode($this->content);

        $fp = fopen($this->path.$this->file,"w");

        fwrite($fp,$data);

        fclose($fp);


    }

    //读取文件
    public function getByFile($file)
    {
        $file = $this->currentPath.$file;
//        var_dump($file);
        if(!file_exists($file))
        {
            die('文件不存在');
        }

        $fp = fopen($file,'r');

        $data = json_decode(fgets($fp,999),true);

        return $data;

    }

    //复制文件
    public function copy($name)
    {
        copy($this->path.$this->file,$this->currentPath.$this->file);
    }

    //获取当前路径
    public function getCurrentPath()
    {
        return $this->currentPath;
    }

    public function getHistoryPath()
    {
        return $this->hitoryPath;
    }

    public function getPath()
    {
        return $this->path;
    }

}