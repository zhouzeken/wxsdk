<?php
namespace zhouzeken\wxsdk;
require '../vendor/autoload.php';
require_once 'config.php';

#有效的accessToken
$token = '67_76LE1tCS0ZFjpmZyFyER7edriXD7pNJt8OiWXQYmoA6gjphjpk7O1F2jecX2f-A9V-T_8PONQsPDeUD3otMRXl9_vc4N6ehsqwbE3eXFRQa-iP5On7USGelf0LgANGaAFANAT';

//获取菜单栏
$config = [
    'appid' => $appid,
    'secret' => $secret,
];
$res = \zhouzeken\wxsdk\Init::getInstance($config)->menu()->getMenu(['access_token'=>$token]);
//print_r($res);
//print_r('<br>');

//创建菜单栏
$config = [
    'appid' => $appid,
    'secret' => $secret,
];
$params = [
    'access_token'=>$token,
    'button' => [
        ['name'=>'菜单1','sub_button'=>[
            ['type'=>'click','name'=>'点击','key'=>'key_111'],
            ['type'=>'view','name'=>'跳转','url'=>'http://www.baidu.com/'],
            ['type'=>'miniprogram','name'=>'小程序','url'=>'http://www.baidu.com/','appid'=>'wx9c1bffcf8ad42fe0','pagepath'=>'pages/homepage/homepage'],
            ['type'=>'scancode_push','name'=>'扫码推事件','key'=>'key_222','sub_button'=>[]],
            ['type'=>'scancode_waitmsg','name'=>'扫码带提示','key'=>'key_333','sub_button'=>[]],
        ]],
        ['name'=>'菜单2','sub_button'=>[
            ['type'=>'pic_sysphoto','name'=>'系统拍照发图','key'=>'key_444','sub_button'=>[]],
        ]]
    ]
];
$res = \zhouzeken\wxsdk\Init::getInstance($config)->menu()->createMenu($params);
print_r($res);
print_r('<br>');

//删除菜单栏
$config = [
    'appid' => $appid,
    'secret' => $secret,
];
$params = [
    'access_token'=>$token,
];
//$res = \zhouzeken\wxsdk\Init::getInstance($config)->menu()->deleteMenu($params);
//print_r($res);
//print_r('<br>');