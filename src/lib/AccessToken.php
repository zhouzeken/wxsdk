<?php
/**
 * 微信AccessToken操作
 * @author zzk
 * @time 2023/4/17
 */
namespace zhouzeken\wxsdk\lib;
class AccessToken
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
     * 获取Access Token
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Get_access_token.html
     * @param $grant_type string 获取access_token填写client_credential
     * @param $appid string 第三方用户唯一凭证
     * @param $secret string 第三方用户唯一凭证密钥，即appsecret
     * @return array
     */
    public function getAccessToken(){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=".$this->config['grant_type']."&appid={$this->config['appid']}&secret={$this->config['secret']}";
        $res = Common::curlSend($url,null,'GET');
        $res = json_decode($res,1);
        return $res;
    }

    /**
     * 获取 Stable Access token
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/getStableAccessToken.html
     * @param $grant_type string 获取access_token填写client_credential
     * @param $appid string 第三方用户唯一凭证
     * @param $secret string 第三方用户唯一凭证密钥，即appsecret
     * @param $force_refresh boolean 默认使用 false。1. force_refresh = false 时为普通调用模式，access_token 有效期内重复调用该接口不会更新 access_token；2. 当force_refresh = true 时为强制刷新模式，会导致上次获取的 access_token 失效，并返回新的 access_token
     * @return array
     */
    public function getStableAccessToken($params=[]){
        $url = 'https://api.weixin.qq.com/cgi-bin/stable_token';
        $params['grant_type'] = $this->config['grant_type'];
        $params['appid'] = $this->config['appid'];
        $params['secret'] = $this->config['secret'];

        $header = [
            'Content-Type: application/json; charset=utf-8'
        ];
        $res = Common::curlSend($url,json_encode($params),'POST',$header);
        $res = json_decode($res,1);
        return $res;
    }

}