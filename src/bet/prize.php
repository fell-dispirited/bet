<?php

namespace bet;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/28
 * Time: 9:30
 */

//奖池
class prize
{
    //奖品内容
    private $content = [
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

    public function __construct()
    {
        $this->path = dirname(__DIR__) . '/file/drawGallery/';
        $this->currentPath = dirname(__DIR__) . '/file/current/';
        $this->file = $this->scanFile();
    }

    //设置奖池
    public function setPool($prize,$num,$name)
    {
        $this->name = $name;
        $this->prize = $prize;
        $this->num = $num;
        //存储奖池
        $this->saveByFile();
        $this->copy($this->file);
    }

//    //获取当前的奖池
//    public function getPool()
//    {
//
//    }

    //存储奖池
    public function save($type)
    {
        //文件存储方式
        if($type == 'file')
        {
            $this->saveByFile();
        }
    }

    public function scanFile()
    {
        $files = scandir($this->currentPath);

        foreach($files as $file)
        {
            if($file!='.'&&$file!='..'&&$file!='pool.txt')
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
    public function getByFile($name)
    {
        $file = $this->currentPath.$name.'.txt';
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

}