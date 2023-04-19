<?php
/**
 * 接收微信推送
 * 对应位置：设置与开发=》基本配置=》服务器配置=》服务器地址(URL)
 */
namespace zhouzeken\wxsdk;
require '../vendor/autoload.php';
require_once 'config.php';

#对应公众号设置的令牌(Token)
$token = 'f0597b058353fc582b0b88dce9f6a11a';

#消息加解密密钥（消息加解密方式为【兼容模式/安全模式】用到）
$EncodingAESKey = 'xxx';

#事件处理逻辑配置,官方文档：https://developers.weixin.qq.com/doc/offiaccount/Custom_Menus/Custom_Menu_Push_Events.html

#推送配置,配合菜单栏，固定参数类型(msgTpl).对应lib/Receive.php 下的MsgTpl方法，其它参数一样也是这个方法
$push_config = [
    #用户关注公众号
    'event.subscribe' => [
        'msgTpl' => 'text',
        'Content' => '感谢关注公众号'
    ],

    #用户取消关注公众号
    'event.unsubscribe' => [
        'msgTpl' => 'text',
        'Content' => '很遗憾您取消关注了，我们下回再见'
    ],

    #点击菜单跳转链接时的事件推送
    'event.VIEW' => [
        'msgTpl' => 'text',
        'Content' => '即将跳转。。。'
    ],

    #点击事件推送(示例1)
    'event.CLICK.key_111' => [
        'msgTpl' => 'location',
        'Location_X' => '坐标x',
        'Location_Y' => '坐标y',
        'Scale' => 33,
        'Label' => 44,
        'MsgId' => 55,

    ],

    #点击事件推送(示例2)
    'event.CLICK.key_222' => [
        'msgTpl' => 'text',
        'Content' => '即将跳转。。。'
    ],
    'event.text.你是大哥' => [
        'msgTpl' => 'text',
        'Content' => '不好意思我不懂。。。'
    ]
];

#用户发送普通文字消息到公众号的回复，根据关键字匹配回复内容
$text_config = [
    '你好,呵呵,大哥' => [
        'msgTpl' => 'text',
        'Content' => '这是回复1'
    ],
    '阿尼,分卷,哎' => [
        'msgTpl' => 'text',
        'Content' => '这是回复2'
    ]
];
$res = Init::getInstance([
    'token' => $token,
    'EncodingAESKey' => $EncodingAESKey
])->receive()->start($push_config,$text_config);
print_r($res);
print_r('<br>');