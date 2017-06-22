<?php

require_once realpath(__DIR__."/../")."/score/config/config.properties.php";
require_once __DIR__."/OauthServer.php";

class Model extends OauthServer
{
    public function __construct()
    {
        parent::__construct();
    }

    function getCorpIdByOpenID($openId)
    {
        return $this->storage->getCorpIdByOpenID($openId);
    }

    function getTokenInfo($token)
    {
        return $this->storage->getAccessToken($token);
    }

    function getPhotos($corp_id)
    {
        $oAlbumEntityActDao = new \AlbumEntityActDao();
        $oAlbumsList = $oAlbumEntityActDao->getAlbums($corp_id);

        $albums_list = array();
        if ($oAlbumsList) foreach($oAlbumsList as $k=>$album){
            $albums_list[$k]['id'] = $album->id;
            $albums_list[$k]['name'] = $album->name;
            $albums_list[$k]['cover'] = $album->getAlbumCover(2);
            $albums_list[$k]['createtime'] = $album->createdtime;
            $albums_list[$k]['visible'] = $album->visible;
        }
        if ( !$albums_list ){
            $aResponse = array('error'=>'相册数据为空');
        }
        else
        {
            $aResponse = array(
                'albumsList' => $albums_list,
            );
        }
        return $aResponse;
    }

    function photosUpload($corp_id)
    {
        if ( empty($_FILES) ){
            return array('error'=>'上传数据为空');
        }

        $albumId = $this->request->query('albums_id');

        if(Empty($albumId)) return array('error'=>'albums_id Not Find');

        \ProduceEnvAssembly::setup();

        //水印 默认添加水印\erm\webroot\uploadify.php
        $water = true;
        if ($water){
            //加水印
            $corporation = \BeanFinder::get("CorpEntityActDao")->getById($corp_id);
            $shuiyin = $corporation->get_shuiyin();
            $position = $shuiyin['position'];
            $type = $shuiyin['content'];
            $s = "";
            $text = "";
            if ($type == 10 || $type == 90){
                $text = $corporation->name;
                $s = "\n";
            }
            if ($type == 20 || $type == 90){
                $text.=$s.str_replace("http://","",\StatUrl::shop_index($corporation->subdomain));
            }
        }

        //开始上传
        $albums_upload = array();
        $oAlbumEntityActDao = new \AlbumEntityActDao();
        foreach ( $_FILES as $name=>$file ) {
            $amount = count($file['name']);
            if ( $amount > 1) {
                for ($i = 0; $i < $amount; $i++) {
                    if ($file[$i]['size'] == 0) {
                        ErrorMessage::setErrorMessage(13200);
                    }
                }
                for ($i = 0; $i < $amount; $i++) {
                    $albums_upload[$i] = $oAlbumEntityActDao->upload($file['tmp_name'][$i], $file['name'][$i], $corp_id, $albumId, $water, $text, $position );
                    $albums_upload[$i]['type'] = $file['type'][$i];
                }
            } else {
                if( $file['size'] == 0 ){
                    ErrorMessage::setErrorMessage(13200);
                }
                $albums_upload = $oAlbumEntityActDao->upload($file['tmp_name'], $file['name'], $corp_id, $albumId, $water, $text, $position );
                $albums_upload['type'] = $file['type'];
            }
        }
        return array(
            'content' => $albums_upload,
        );
    }

    function getPhotosList($corp_id)
    {
        $oAlbumEntityActDao = new \AlbumEntityActDao();
        $total = 0;
        $oAlbumsList = $oAlbumEntityActDao->getPhotosByTime($corp_id,'','',1,20,'',$total);

        if ( !$oAlbumsList ){
            $aResponse = array('error'=>'相册数据为空');
        }
        else
        {
            $aResponse = array(
                'PhotosList' => $oAlbumsList,
            );
        }

        return $aResponse;
    }
}