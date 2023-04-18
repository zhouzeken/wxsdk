<?php
/**
 * 事件推送
 * @author zzk
 * @time 2023/4/17
 */
namespace zhouzeken\wxsdk\lib;
class Receive
{
    private static $instance = null;
    private $config = [
        'token'                         => '',
        'EncodingAESKey'                        => '',
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
     * 事件推送
     * @param $push_config array 不同事件的处理逻辑配置
     * @return void
     */
    public function start($push_config=[]){
        if (isset($_GET['echostr'])) {
            #验证url
            $this->check();
        } else {
            #接收推送并处理
            $this->push($push_config);
        }
    }


    //验证url
    private function check(){
        $t = $this->checkSign([
            'signature' => $_GET['signature'],
            'timestamp' => $_GET['timestamp'],
            'nonce' => $_GET['nonce'],
            'token' => $this->config['token']
        ]);

        if($t === true){
            header('content-type:text');
            echo $_GET["echostr"];
            exit;
        }else{
            header('content-type:text');
            echo '';
            exit;
        }
    }

    /**
     * 接收推送
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Custom_Menu_Push_Events.html
     * @param string $signature 加密串
     * @param string $timestamp 时间戳
     * @param string $nonce 随机数
     * @param string $token 在公众号后台定义好的令牌(Token)
     * @return bool
     */
    private function push($push_config){
        $postStr = @$GLOBALS['HTTP_RAW_POST_DATA'];
        if(empty($postStr)) {
            $postStr = file_get_contents("php://input");
        }

        file_put_contents('receive.log',$postStr);

        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName; #用户OPEN_ID
            $toUsername = $postObj->ToUserName; #服务号ID
            $keyword = trim($postObj->Content);
            $MsgType = $postObj->MsgType;
            $Event = $postObj->Event;
            $Content = $postObj->Content;
            $EventKey = $postObj->EventKey;

            #最终执行的命令
            $shell = $MsgType;
            if(!empty($Event)){
                $shell .= $Event;
            }

            if(!empty($Event) && $EventKey){
                $shell .= $Event;
            }

            #默认发送
            $default = [
                'msgTpl' => 'text',
                'Content' => '即将跳转。。。'
            ];

            #匹配发送模板
            $shell = isset($push_config[$shell]) ? $push_config[$shell] : $default;
            $textTpl = $this->MsgTpl($shell['msgTpl']);

            $time = time();

            #默认参数：1类型，2发送人，3接收人，4时间戳
            $values = [$textTpl, $fromUsername, $toUsername, $time];

            #把参数组合起来再回调sprintf参数
            $values = array_merge($values,array_values($shell));
            $resultStr = call_user_func_array('sprintf',$values);
            echo $resultStr;
        } else {
            echo "";
            exit;
        }
    }

    /**
     * 签名验证
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Access_Overview.html
     * @param string $signature 加密串
     * @param string $timestamp 时间戳
     * @param string $nonce 随机数
     * @param string $token 在公众号后台定义好的令牌(Token)
     * @return bool
     */
    private function checkSign($params=[]){
        $signature = $params["signature"];
        $timestamp = $params["timestamp"];
        $nonce = $params["nonce"];
        $token = $params['token'];

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 微信消息模板
     * @param null $type
     * @return string
     */
    private function MsgTpl($type = null){
        $tpl = '';

        switch($type){
            case 'image':
                //图片消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Image>";
                $tpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $tpl .= "</Image>";
                $tpl .= "</xml>";
                break;

            case 'voice':
                //语音消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $tpl .= "<Format><![CDATA[%s]]></Format>";
                $tpl .= "<MsgId>%s</MsgId>";
                $tpl .= "</xml>";
                break;

            case 'video':
                //视频消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $tpl .= "<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>";
                $tpl .= "<MsgId>%s</MsgId>";
                $tpl .= "</xml>";
                break;

            case 'shortvideo':
                //小视频消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $tpl .= "<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>";
                $tpl .= "<MsgId>%s</MsgId>";
                $tpl .= "</xml>";
                break;

            case 'location':
                //地理位置消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Location_X>%s</Location_X>";
                $tpl .= "<Location_Y>%s</Location_Y>";
                $tpl .= "<Scale>%s</Scale>";
                $tpl .= "<Label><![CDATA[%s]]></Label>";
                $tpl .= "<MsgId>%s</MsgId>";
                $tpl .= "</xml>";
                break;

            case 'link':
                //链接消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Title><![CDATA[%s] ]></Title>";
                $tpl .= "<Description><![CDATA[%s]]></Description>";
                $tpl .= "<Url><![CDATA[%s]]></Url>";
                $tpl .= "<MsgId>%s</MsgId>";
                $tpl .= "</xml>";
                break;

            case 'CLICK':
                //点击菜单拉取消息时的事件推送
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Event><![CDATA[event]]></Event>";
                $tpl .= "<EventKey><![CDATA[CLICK]]></EventKey>";
                $tpl .= "</xml>";
                break;

            case 'VIEW':
                //点击菜单跳转链接时的事件推送
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Event><![CDATA[event]]></Event>";
                $tpl .= "<EventKey><![CDATA[VIEW]]></EventKey>";
                $tpl .= "<MenuId>%s</MenuId>";
                $tpl .= "</xml>";
                break;

            case 'scancode_push':
                //scancode_push：扫码推事件的事件推送
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Event><![CDATA[event]]></Event>";
                $tpl .= "<EventKey><![CDATA[scancode_push]]></EventKey>";
                $tpl .= "<ScanCodeInfo><ScanType><![CDATA[%s]]></ScanType>";
                $tpl .= "<ScanResult><![CDATA[%s]]></ScanResult>";
                $tpl .= "</ScanCodeInfo>";
                $tpl .= "</xml>";
                break;

            case 'scancode_waitmsg':
                //scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框的事件推送
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Event><![CDATA[event]]></Event>";
                $tpl .= "<EventKey><![CDATA[scancode_waitmsg]]></EventKey>";
                $tpl .= "<ScanCodeInfo><ScanType><![CDATA[%s]]></ScanType>";
                $tpl .= "<ScanResult><![CDATA[%s]]></ScanResult>";
                $tpl .= "</ScanCodeInfo>";
                $tpl .= "</xml>";
                break;

            case 'pic_sysphoto':
                //pic_sysphoto：弹出系统拍照发图的事件推送
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Event><![CDATA[event]]></Event>";
                $tpl .= "<EventKey><![CDATA[pic_sysphoto]]></EventKey>";
                $tpl .= "<SendPicsInfo><Count>%s</Count>";
                $tpl .= "<PicList><item><PicMd5Sum><![CDATA[%s]]></PicMd5Sum>";
                $tpl .= "</item>";
                $tpl .= "</PicList>";
                $tpl .= "</SendPicsInfo>";
                $tpl .= "</xml>";
                break;

            case 'pic_photo_or_album':
                //pic_photo_or_album：弹出拍照或者相册发图的事件推送
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Event><![CDATA[event]]></Event>";
                $tpl .= "<EventKey><![CDATA[pic_photo_or_album]]></EventKey>";
                $tpl .= "<SendPicsInfo><Count>%s</Count>";
                $tpl .= "<PicList><item><PicMd5Sum><![CDATA[%s]]></PicMd5Sum>";
                $tpl .= "</item>";
                $tpl .= "</PicList>";
                $tpl .= "</SendPicsInfo>";
                $tpl .= "</xml>";
                break;

            case 'pic_weixin':
                //pic_weixin：弹出微信相册发图器的事件推送
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Event><![CDATA[pic_weixin]]></Event>";
                $tpl .= "<EventKey><![CDATA[%s]]></EventKey>";
                $tpl .= "<SendPicsInfo><Count>%s</Count>";
                $tpl .= "<PicList><item><PicMd5Sum><![CDATA[%s]]></PicMd5Sum>";
                $tpl .= "</item>";
                $tpl .= "</PicList>";
                $tpl .= "</SendPicsInfo>";
                $tpl .= "</xml>";
                break;

            case 'location_select':
                //location_select：弹出地理位置选择器的事件推送
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Event><![CDATA[location_select]]></Event>";
                $tpl .= "<EventKey><![CDATA[%s]]></EventKey>";
                $tpl .= "<SendLocationInfo><Location_X><![CDATA[%s]]></Location_X>";
                $tpl .= "<Location_Y><![CDATA[%s]]></Location_Y>";
                $tpl .= "<Scale><![CDATA[%s]]></Scale>";
                $tpl .= "<Label><![CDATA[%s]]></Label>";
                $tpl .= "<Poiname><![CDATA[%s]]></Poiname>";
                $tpl .= "</SendLocationInfo>";
                $tpl .= "</xml>";
                break;

            case 'text':
            default:
                //文本信息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Content><![CDATA[%s]]></Content>";
                $tpl .= "<FuncFlag>0</FuncFlag>";
                $tpl .= "</xml>";
        }
        return $tpl;
    }
}