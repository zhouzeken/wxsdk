<?php
/**
 * token类调用示例
 */
namespace zhouzeken\wxsdk;
require '../vendor/autoload.php';
require_once 'config.php';

//获取Access Token
$config = [
    'appid' => $appid,
    'secret' => $secret,
    'grant_type' => 'client_credential'
];
//$res = Init::getInstance($config)->AccessToken()->getAccessToken();
//print_r($res);
//print_r('<br>');

//获取Stable Access token
$config = [
    'appid' => $appid,
    'secret' => $secret,
    'grant_type' => 'client_credential',
    'force_refresh' => false
];
$res = Init::getInstance($config)->AccessToken()->getStableAccessToken();
print_r($res);
print_r('<br>');