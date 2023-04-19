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
     * 接收事件推送
     * @param $push_config 接收到事件推送后的回复
     * @param $text_config #接收普通消息后的回复
     * @return void
     */
    public function start($push_config=[],$text_config=[]){
        if (isset($_GET['echostr'])) {
            #验证url
            $this->check();
        } else {
            #接收推送并处理
            $this->push($push_config,$text_config);
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
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Receiving_event_pushes.html
     * @param string $signature 加密串
     * @param string $timestamp 时间戳
     * @param string $nonce 随机数
     * @param string $token 在公众号后台定义好的令牌(Token)
     * @return bool
     */
    private function push($push_config=[],$text_config=[]){
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : false;
        if(empty($postStr)) {
            $postStr = file_get_contents("php://input");
        }

        file_put_contents('./runtime/receive.log',$postStr);

        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = (array)$postObj->FromUserName; #用户OPEN_ID
            $toUsername = (array)$postObj->ToUserName; #服务号ID
            $keyword = (array)$postObj->Content;
            $MsgType = (array)$postObj->MsgType;
            $Event = (array)$postObj->Event;
            $EventKey = (array)$postObj->EventKey;

            #最终执行的命令
            $shell = $MsgType[0];
            if(!empty($Event)){
                $shell .= '.'.$Event[0];
            }

            if(!empty($Event) && $EventKey){
                $shell .= '.'.$EventKey[0];
            }

            #默认发送
            $default = [
                'msgTpl' => 'text',
                'Content' => '我不是很理解。。。'
            ];

            #匹配发送模板
            switch ($MsgType){
                case 'event':
                    #事件推送-回复
                    $shell = isset($push_config[$shell]) ? $push_config[$shell] : $default;
                    break;
                case 'text':
                    #普通消息接收-回复
                    $shell = $default;
                    foreach ($text_config as $key=>$value){
                        if(in_array($keyword,$key)){
                            $shell = $value;
                            break;
                        }
                    }
                    break;
                case 'image':
                    #图片消息-回复
                    $shell = $default;
                    break;
                case 'voice':
                    #语音消息-回复
                    $shell = $default;
                    break;
                case 'video':
                    #视频消息-回复
                    $shell = $default;
                    break;
                case 'shortvideo':
                    #小视频消息-回复
                    $shell = $default;
                    break;
                case 'location':
                    #地理位置消息-回复
                    $shell = $default;
                    break;
                case 'link':
                    #链接消息-回复
                    $shell = $default;
                    break;
                default:
                    #默认回复
                    $shell = $default;
                    break;
            }

            $textTpl = $this->MsgTpl($shell['msgTpl']);

            $time = time();

            #默认参数：1类型，2发送人，3接收人，4时间戳
            $values = [$textTpl, $fromUsername[0], $toUsername[0], $time];

            #把参数组合起来再回调sprintf参数
            $values = array_merge($values,array_values($shell));
            $resultStr = call_user_func_array('sprintf',$values);
            echo $resultStr;
            file_put_contents('./runtime/receive2.log',$resultStr);
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
     * 被动恢复消息模板
     * 官方文档：https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Passive_user_reply_message.html
     * @param null $type
     * @return string
     */
    private function MsgTpl($type = null){
        $tpl = '';

        switch($type){
            case 'image':
                //回复-图片消息
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
                //回复-语音消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Voice>";
                $tpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $tpl .= "</Voice>";
                $tpl .= "</xml>";
                break;

            case 'video':
                //回复-视频消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Video>";
                $tpl .= "<MediaId><![CDATA[%s]]></MediaId>";
                $tpl .= "<Title><![CDATA[%s]]></Title>";
                $tpl .= "<Description><![CDATA[%s]]></Description>";
                $tpl .= "</Video>";
                $tpl .= "</xml>";
                break;
            case 'music':
                //回复-音乐消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<Music>";
                $tpl .= "<Title><![CDATA[%s]]></Title>";
                $tpl .= "<Description><![CDATA[%s]]></Description>";
                $tpl .= "<MusicUrl><![CDATA[%s]]></MusicUrl>";
                $tpl .= "<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>";
                $tpl .= "<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>";
                $tpl .= "</Music>";
                $tpl .= "</xml>";
                break;
            case 'news':
                //回复-图文消息
                $tpl .= "<xml>";
                $tpl .= "<ToUserName><![CDATA[%s]]></ToUserName>";
                $tpl .= "<FromUserName><![CDATA[%s]]></FromUserName>";
                $tpl .= "<CreateTime>%s</CreateTime>";
                $tpl .= "<MsgType><![CDATA[%s]]></MsgType>";
                $tpl .= "<ArticleCount>1</ArticleCount>";
                $tpl .= "<Articles>";
                $tpl .= "<item>";
                $tpl .= "<Title><![CDATA[%s]]></Title>";
                $tpl .= "<Description><![CDATA[%s]]></Description>";
                $tpl .= "<PicUrl><![CDATA[%s]]></PicUrl>";
                $tpl .= "<Url><![CDATA[%s]]></Url>";
                $tpl .= "</item>";
                $tpl .= "</Articles>";
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
                $tpl .= "</xml>";
        }
        return $tpl;
    }
}