<?php

$appid     = "wx67f26bd20297bc03";
$appsecret = '9b696f9113779f1eb78465211d77c0f0';

function getAccessToken($appid, $appsecret)
{
    $rst      = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret);
    $rst_json = json_decode($rst, true);
    return $rst_json;

}

function getIpList($accesstoken)
{
    $rst      = file_get_contents("https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=" . $accesstoken);
    $rst_json = json_decode($rst, true);
    return $rst_json;
}

$AccessToken = getAccessToken($appid, $appsecret);

if (!isset($AccessToken['errcode'])) {
    print_r($AccessToken);
} else {
    echo $AccessToken['errmsg'];
    exit;
}

$IpList = getIpList($AccessToken['access_token']);

if (!isset($IpList['errcode'])) {
    print_r($IpList);
} else {
    echo $IpList['errmsg'];
    exit;
}

print_r($IpList);
