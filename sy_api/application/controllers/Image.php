<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-17
 * Time: 下午10:23
 */
class ImageController extends CommonController {
    public $signStatus = false;

    private $acceptTypes = [
        'image/gif' => 'gif',
        'image/png' => 'png',
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpg',
    ];

    public function init() {
        parent::init();
        $this->signStatus = false;
    }

    public function indexAction() {
        $res = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/Image/index', $_GET);
        $resArr = \Tool\Tool::jsonDecode($res);
        if ($resArr['code'] > 0) {
            $this->sendRsp($res);
        } else {
            $this->sendRsp($resArr['data']);
        }
    }

    public function createQrImageAction() {
        $res = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/Image/createQrImage', $_GET);
        $this->sendRsp($res);
    }

    /**
     * 上传图片
     * @SyFilter-{"field": "upload_type","explain": "上传类型","type": "int","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "image_base64","explain": "图片base64内容","type": "string","rules": {"baseimage": 1}}
     * @SyFilter-{"field": "image_url","explain": "图片链接","type": "string","rules": {"url": 1}}
     * @SyFilter-{"field": "image_wxmedia","explain": "微信媒体ID","type": "string","rules": {"min": 1}}
     */
    public function uploadImageAction() {
        //思想-不管何种方式的图片上传,都转换成base64编码传递给services服务
        //上传类型 1:文件上传 2:base64上传 3:url上传 4:微信媒体上传
        $uploadType = (int)\Request\SyRequest::getParams('upload_type');
        $handleRes = \Dao\ApiImageDao::uploadImageHandle($uploadType);
        $uploadRes = \SyModule\SyModuleService::getInstance()->sendApiReq('/Index/Image/uploadImage', $handleRes);
        $this->sendRsp($uploadRes);
    }
}