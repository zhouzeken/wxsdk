<?php
/**
 * 微信封装初始化
 */
namespace zhouzeken\wxsdk;

class Init
{
    private static $instance = null;
    private $config = [
        'appid'                         => '',
        'secret'                        => '',
    ];

    private function __construct($c=[])
    {
        $this->config = array_merge($this->config,$c);
    }

    private function __clone()
    {

    }

    public static function getInstance($config=[])
    {
        if(empty(self::$instance)){
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    //引用AccessToken类
    public function AccessToken(){
        return \zhouzeken\wxsdk\lib\AccessToken::getInstance($this->config);
    }

    //引用Menu类
    public function menu(){
        return \zhouzeken\wxsdk\lib\Menu::getInstance($this->config);
    }

    //引用事件推送类
    public function receive(){
        return \zhouzeken\wxsdk\lib\Receive::getInstance($this->config);
    }


}