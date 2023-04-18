<?php
/**
 * 公共
 * @author zzk
 * @time 2023/4/17
 */
namespace zhouzeken\wxsdk\lib;
class Common
{
    /**
     * curl请求
     * @param $url string 请求地址
     * @param $data array|string 请求参数
     * @param $method string 请求方式
     * @param $header array 请求头
     * @param $timeout int 超时时间
     * @return string
     */
    public static function curlSend($url = '', $data = null, $method='POST', $header = [],$timeout=60){
        $ch = curl_init();
        if(strpos($url,'https://') !== false){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }
        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        switch($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                break;
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}