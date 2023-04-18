<?php
/**
 * 自定义菜单
 * @author zzk
 * @time 2023/4/17
 */
namespace zhouzeken\wxsdk\lib;
class Menu
{
    private static $instance = null;
    private $config = [
        'appid'                         => '',
        'secret'                        => '',
        'grant_type'                    => 'client_credential'
    ];
    private function __construct($c)
    {
        $this->config = array_merge($this->config,$c);
    }

    private function __clone()
    {

    }

    //初始化
    public static function getInstance($config)
    {
        if(empty(self::$instance)){
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /**
     * 创建接口
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Creating_Custom-Defined_Menu.html
     * @param $access_token string access_token
     * @param $button array 菜单设置数组，具体看官方文档
     * @return array
     */
    public function createMenu($params=[]){
        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$params['access_token'];
        $header = [
            'Content-Type: application/json; charset=utf-8'
        ];
        $params = json_encode($params,JSON_UNESCAPED_UNICODE);#JSON_UNESCAPED_UNICODE防止中文进行unicode编码
        $res = Common::curlSend($url,$params,'POST',$header);
        $res = json_decode($res,1);
        return $res;
    }

    /**
     * 查询接口
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Querying_Custom_Menus.html
     * @param $access_token string access_token
     * @return array
     */
    public function getMenu($params=[]){
        $url = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token='.$params['access_token'];
        $header = [
            'Content-Type: application/json; charset=utf-8'
        ];
        $res = Common::curlSend($url,json_encode($params),'POST',$header);
        $res = json_decode($res,1);
        return $res;
    }

    /**
     * 删除接口
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Deleting_Custom-Defined_Menu.html
     * @param $access_token string access_token
     * @return array
     */
    public function deleteMenu($params=[]){
        $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$params['access_token'];
        $res = Common::curlSend($url,null,'GET');
        $res = json_decode($res,1);
        return $res;
    }

}