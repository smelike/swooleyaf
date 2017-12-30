<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-11-4
 * Time: 下午10:32
 */
namespace Tool\Image;

use Constant\ErrorCode;
use Constant\Server;
use Exception\Image\ImageException;

abstract class SyImageBase {
    /**
     * 图片sha1值
     * @var string
     */
    protected $sha1 = '';
    /**
     * 图片类型
     * @var string
     */
    protected $mimeType = '';
    /**
     * 图片类型
     * @var string
     */
    protected $ext = '';
    /**
     * 图片的宽
     * @var int
     */
    protected $width = 0;
    /**
     * 图片的高
     * @var int
     */
    protected $height = 0;
    /**
     * 图片的大小,单位为字节
     * @var int
     */
    protected $size = 0;
    /**
     * 图片质量,取值1-100,数值越大质量越好
     * @var int
     */
    protected $quality = 0;

    /**
     * @param string $byteStr 图片二进制流字符串
     * @throws \Exception\Image\ImageException
     */
    public function __construct(string $byteStr) {
        $imageInfo = getimagesize('data://application/octet-stream;base64,' . base64_encode($byteStr));
        if ($imageInfo === false) {
            throw new ImageException('解析图片失败', ErrorCode::IMAGE_UPLOAD_PARAM_ERROR);
        } else if (!in_array($imageInfo[2], [1, 2, 3])) {
            throw new ImageException('图片类型不支持', ErrorCode::IMAGE_UPLOAD_PARAM_ERROR);
        }

        $this->sha1 = sha1($byteStr);
        $this->size = strlen($byteStr);
        $this->width = (int)$imageInfo[0];
        $this->height = (int)$imageInfo[1];
        if ($imageInfo[2] == 1) {
            $this->mimeType = Server::IMAGE_MIME_TYPE_GIF;
            $this->ext = 'gif';
        } else if ($imageInfo[2] == 2) {
            $this->mimeType = Server::IMAGE_MIME_TYPE_JPEG;
            $this->ext = 'jpg';
        } else {
            $this->mimeType = Server::IMAGE_MIME_TYPE_PNG;
            $this->ext = 'png';
        }
    }

    /**
     * 获取图片sha1值
     * @return string
     */
    public function getSha1() : string {
        return $this->sha1;
    }

    /**
     * 获取图片mime类型
     * @return string
     */
    public function getMimeType() : string {
        return $this->mimeType;
    }

    /**
     * 获取图片扩展名
     * @return string
     */
    public function getExt() : string {
        return $this->ext;
    }

    /**
     * 获取图片宽度
     * @return int
     */
    public function getWidth() : int {
        return $this->width;
    }

    /**
     * 获取图片高度
     * @return int
     */
    public function getHeight() : int {
        return $this->height;
    }

    /**
     * 获取图片原始大小
     * @return int
     */
    public function getSize() : int {
        return $this->size;
    }

    /**
     * 获取图片质量
     * @return int
     */
    public function getQuality() : int {
        return $this->quality;
    }

    /**
     * 检测图片
     * @param string $filePath 图片路径
     * @param int $type 图片类型 1:水印图片
     * @return array|bool
     * @throws \Exception\Image\ImageException
     */
    protected function checkImage(string $filePath,int $type) {
        $imageInfo = getimagesize($filePath);
        if($imageInfo === false){
            throw new ImageException('读取图片失败', ErrorCode::IMAGE_UPLOAD_PARAM_ERROR);
        }

        $imageTypes = $type == 1 ? [2, 3] : [1, 2, 3];
        if(!in_array($imageInfo[2], $imageTypes)){
            throw new ImageException('图片类型不支持', ErrorCode::IMAGE_UPLOAD_PARAM_ERROR);
        }

        return $imageInfo;
    }

    /**
     * 检测透明度
     * @param int $alpha 透明度
     * @return int
     */
    protected function checkImageAlpha(int $alpha) {
        if ($alpha < 0) {
            return 0;
        } else if($alpha > 100){
            return 100;
        } else {
            return $alpha;
        }
    }

    /**
     * 缩略图片
     * @param int $width 缩略后的宽度
     * @param int $height 缩略后的高度
     * @return $this
     */
    abstract public function resizeImage(int $width,int $height);
    /**
     * 设置图片质量
     * @param int $quality 压缩质量,1-100,数字越大压缩质量越好
     * @return $this
     * @throws \Exception\Image\ImageException
     */
    abstract public function setQuality(int $quality);
    /**
     * 添加文本水印
     * @param string $txt 文本内容
     * @param int $startX 文本起始横坐标
     * @param int $startY 文本起始纵坐标
     * @param \Tool\Image\SyFont $font 字体信息对象
     * @return $this
     * @throws \Exception\Image\ImageException
     */
    abstract public function addWaterTxt(string $txt,int $startX,int $startY,SyFont $font);

    /**
     * 添加图片水印
     * @param string $filePath 图片路径
     * @param int $startX 起始横坐标
     * @param int $startY 起始纵坐标
     * @param int $alpha 透明度
     * @return $this
     * @throws \Exception\Image\ImageException
     */
    abstract public function addWaterImage(string $filePath,int $startX,int $startY,int $alpha);
    /**
     * 写图片文件
     * @param string $path 文件目录
     * @return string
     * @throws \Exception\Image\ImageException
     */
    abstract public function writeImage(string $path);
}