<?php
namespace zhouzeken\wxsdk;
require '../vendor/autoload.php';

$c1 = [
    'appid'                         => '11',
    'secret'                        => '111',
    'grant_type'                    => '1111'
];
$i2 = Init::getInstance($c1);
$t2 = $i2->token()->getAccessToken();
//print_r($r1);
print_r("<br>");
print_r($t2);

