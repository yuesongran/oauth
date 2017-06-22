<?php

require_once __DIR__."/Model.php";

$model = new Model();
$flag = true;
$data = array();

$action = $model->request -> query('action');
$openID = $model->request -> query('openID');
$token =  $model->request -> query('token');
if(Empty($action) || Empty($openID) || Empty($token))
    print_json(array('error'=>'params invalid','message'=>'参数不齐'));

$tokenInfo = $model->getTokenInfo($token);
if($tokenInfo['expires']<=time())
    print_json(array('error'=>'expires invalid'));

if(!$corpid = $model->getCorpIdByOpenID($openID))
    print_json(array('error'=>'openID invalid'));

if($flag)
{
    switch($action)
    {
        case 'photos':
            $data = $model -> getPhotos($corpid);
            break;
        case 'photosUpload':
            $data = $model -> photosUpload($corpid);
            break;
        case 'photosList':
            $data = $model -> getPhotosList($corpid);
            break;
        default:
            $data = array('type invalid','message'=>'请求数据类型不存在');
            break;
    }
}

print_json($data);

function print_json($data)
{
    die(json_encode($data));
}