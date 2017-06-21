<?php

session_start();
require_once __DIR__."/OauthServer.php";

if(isset($_GET['code']) && $_GET['code'])
{
    $scope ='';
    $data['client_id'] = "testclient";
    $data['client_secret'] = "testpass";
    $data['grant_type'] = "authorization_code";
    $data['scope'] = $scope;
    $data['code'] = $_GET['code'];
    $data['redirect_uri'] = "http://localhost/oauth/b.php";
    $url = "http://localhost/oauth/Token.php";
    $token = curl($url,$data);
    $_SESSION['token'] = json_decode($token,true);
    header("location:".$data['redirect_uri']);
}
else
{
    var_dump("第一次TOKEN");
    var_dump($_SESSION['token']);
    $scope ='';
    $data['client_id'] = "testclient";
    $data['client_secret'] = "testpass";
    $data['grant_type'] = "refresh_token";
    $data['scope'] = $scope;
    $data['refresh_token'] = $_SESSION['token']['refresh_token'];
    $data['redirect_uri'] = "http://localhost/oauth/b.php";
    $url = "http://localhost/oauth/Token.php";
    $token = curl($url,$data);
    $_SESSION['token'] = json_decode($token,true);
    var_dump("刷新TOKEN");
    var_dump($_SESSION['token']);
}

$server = new OauthServer();
$userInfo = $server -> getUserInfo($_SESSION['token']['access_token']);
var_dump("通过有效TOken请求API");
var_dump($userInfo);


function curl($url,$data=array())
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if($data)
    {
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}