<?php
/**
 * 图片控制器
 * User: Administrator
 * Date: 2017-04-16
 * Time: 18:02
 */
class ImageController extends CommonController {
    public function init() {
        parent::init();
    }

    /**
     * 获取百度编辑器配置
     * @api {get} /Image/index 获取百度编辑器配置
     * @apiDescription 获取百度编辑器配置
     * @apiGroup ServiceImage
     * @apiParam {string{1..32}} callback 回调函数名称
     * @apiSuccess {string} Body jsonp字符串
     * @apiUse CommonFail
     * @SyFilter-{"field": "callback","explain": "回调函数","type": "string","rules": {"required": 1,"min": 1,"max": 150}}
     */
    public function indexAction() {
        $jsonpStr = \Request\SyRequest::getParams('callback', '') . '(' . \Tool\Tool::jsonEncode(\Yaconf::get( 'ueditor.' . SY_ENV)) . ')';
        $this->SyResult->setData($jsonpStr);

        $this->sendRsp();
    }

    /**
     * 生成二维码图片
     * @api {get} /Image/createQrImage 生成二维码图片
     * @apiDescription 生成二维码图片
     * @apiGroup ServiceImage
     * @apiParam {string{1..255}} url 链接地址
     * @apiParam {string} error_level 容错级别，取值为H L M Q，越在前级别越低
     * @apiParam {number{1-10}} image_size 图片大小
     * @apiParam {number{0-200}} margin_size 外边框间隙，单位为px
     * @apiSuccess {string} Body 图片字节流
     * @apiUse CommonFail
     * @SyFilter-{"field": "url","explain": "链接地址","type": "string","rules": {"required": 1,"url": 1}}
     * @SyFilter-{"field": "error_level","explain": "容错级别","type": "string","rules": {"regex": "/^[HLMQ]{1}$/"}}
     * @SyFilter-{"field": "image_size","explain": "图片大小","type": "int","rules": {"min": 1,"max": 10}}
     * @SyFilter-{"field": "margin_size","explain": "外边框间隙","type": "int","rules": {"min": 0,"max": 200}}
     */
    public function createQrImageAction() {
        $allParams = \Request\SyRequest::getParams();
        ob_start();
        \Qrcode\SyQrCode::createImage($allParams['url'], [
            'error_level' => \Request\SyRequest::getParams('error_level', \Qrcode\SyQrCode::QR_ERROR_LEVEL_ONE),
            'image_size' => (int)\Request\SyRequest::getParams('image_size', 5),
            'margin_size' => (int)\Request\SyRequest::getParams('margin_size', 2),
        ]);
        $image = ob_get_contents();
        ob_end_clean();

        $this->SyResult->setData([
            'image' => 'data:image/png;base64,' . base64_encode($image),
        ]);

        $this->sendRsp();
    }

    /**
     * 生成验证码图片
     * @api {get} /Image/createCodeImage 生成验证码图片
     * @apiDescription 生成验证码图片
     * @apiGroup ServiceImage
     * @apiParam {string{1..64}} _sytoken 令牌标识
     * @apiParam {number{50-150}} image_width 图片宽度
     * @apiParam {number{20-80}} image_height 图片高度
     * @apiSuccess {string} Body 图片字节流
     * @apiUse CommonFail
     * @SyFilter-{"field": "_sytoken","explain": "令牌标识","type": "string","rules": {"required": 1,"min": 1,"max": 150}}
     * @SyFilter-{"field": "image_width","explain": "图片宽度","type": "int","rules": {"required": 1,"min": 50,"max": 150}}
     * @SyFilter-{"field": "image_height","explain": "图片高度","type": "int","rules": {"required": 1,"min": 20,"max": 80}}
     */
    public function createCodeImageAction() {
        $fontPath = \SyServer\HttpServer::getServerConfig('storepath_resources') . '/consolas.ttf';
        //创建图片
        $imageWidth = (int)\Request\SyRequest::getParams('image_width', 130);
        $imageHeight = (int)\Request\SyRequest::getParams('image_height', 45);
        $image = imagecreate($imageWidth, $imageHeight);
        imagecolorallocate($image, rand(50, 200), rand(0, 155), rand(0, 155)); //第一次对 imagecolorallocate() 的调用会给基于调色板的图像填充背景色
        $fontColor = imageColorAllocate($image, 255, 255, 255);   //字体颜色
        $code = '';
        //产生随机字符
        for ($i = 0; $i < 4; $i++) {
            $randAsciiNumArray = array(rand(48, 57), rand(65, 90));
            $randAsciiNum = $randAsciiNumArray[rand(0, 1)];
            $randStr = chr($randAsciiNum);
            imagettftext($image, 30, rand(0, 20) - rand(0, 25), 5 + $i * 30, rand(30, 35), $fontColor, $fontPath, $randStr);
            $code .= $randStr;
        }
        //干扰线
        for ($i = 0; $i < 8; $i++) {
            $lineColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            imageline($image, rand(0, $imageWidth), 0, rand(0, $imageWidth), $imageHeight, $lineColor);
        }
        //干扰点
        for ($i = 0; $i < 250; $i++) {
            imagesetpixel($image, rand(0, $imageWidth), rand(0, $imageHeight), $fontColor);
        }

        ob_start();
        imagepng($image);
        $imageContent = ob_get_contents();
        ob_end_clean();

        imagedestroy($image);

        //随机字符串放入redis
        $redis = \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance();
        $redisKey = \Constant\Server::REDIS_PREFIX_CODE_IMAGE . \Request\SyRequest::getParams('_sytoken');
        $redis->set($redisKey, $code, 190);

        $this->SyResult->setData([
            'image' => 'data:image/png;base64,' . $imageContent,
        ]);

        $this->sendRsp();
    }

    /**
     * 图片上传
     * @api {post} /Image/uploadImage 图片上传
     * @apiDescription 图片上传
     * @apiGroup ServiceImage
     * @apiParam {string} upload_type 上传类型,4位长度字符串
     * @apiUse ServiceImageUploadTypeBase64
     * @apiUse ServiceImageUploadTypeUrl
     * @apiUse ServiceImageUploadContentNormal
     * @apiUse ServiceImageUploadContentPuzzle
     * @apiUse CommonSuccess
     * @apiUse CommonFail
     * @SyFilter-{"field": "upload_type","explain": "上传类型","type": "int","rules": {"required": 1,"min": 1}}
     * @SyFilter-{"field": "image_width","explain": "图片限制宽度","type": "int","rules": {"required": 1,"min": 1,"max": 5000}}
     * @SyFilter-{"field": "image_height","explain": "图片限制高度","type": "int","rules": {"required": 1,"min": 1,"max": 5000}}
     */
    public function uploadImageAction() {
        $cacheKey = \Constant\Server::REDIS_PREFIX_IMAGE_DATA . \Request\SyRequest::getParams('_syfile_tag', '');
        $cacheData = \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance()->get($cacheKey);
        if ($cacheData === false) {
            $this->SyResult->setCodeMsg(\Constant\ErrorCode::COMMON_SERVER_ERROR, '图片缓存内容不存在');
        } else {
            \DesignPatterns\Factories\CacheSimpleFactory::getRedisInstance()->del($cacheKey);
            $imageWidth = (int)\Request\SyRequest::getParams('image_width');
            $imageHeight = (int)\Request\SyRequest::getParams('image_height');
            $syImage = new \Tool\Image\SyImageImagick($cacheData);
            $fileName = $syImage->resizeImage($imageWidth, $imageHeight)
                ->setQuality(100)
                ->writeImage(\SyServer\BaseServer::getServerConfig('storepath_cache'));
            $this->SyResult->setData([
                'file_name' => $fileName,
            ]);
        }

        $this->sendRsp();
    }
}