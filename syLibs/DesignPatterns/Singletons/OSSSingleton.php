<?php
/**
 * OSS单例类
 * User: 姜伟
 * Date: 2017/1/6 0006
 * Time: 9:38
 */
namespace DesignPatterns\Singletons;

use Constant\ErrorCode;
use Exception\OSS\OSSException;
use Log\Log;
use OSS\OssClient;
use Tool\Tool;
use Traits\SingletonTrait;

class OSSSingleton {
    use SingletonTrait;

    /**
     * @var string
     */
    private $bucketName = '';
    /**
     * @var string
     */
    private $bucketDomain = '';
    /**
     * 内网访问客户端
     * @var \OSS\OssClient
     */
    private $innerClient = null;
    /**
     * 外网访问客户端
     * @var \OSS\OssClient
     */
    private $outerClient = null;

    private function __construct() {
        $this->init();
    }

    /**
     * @return \DesignPatterns\Singletons\OSSSingleton
     */
    public static function getInstance() {
        if(is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * 初始化
     * @throws OSSException
     */
    private function init() {
        $configs = \Yaconf::get('oss.' . SY_ENV);

        $keyId = (string)Tool::getArrayVal($configs, 'access.key.id', '', true);
        if (strlen($keyId) > 0) {
            throw new OSSException('帐号ID不能为空', ErrorCode::OSS_PARAM_ERROR);
        }

        $keySecret = (string)Tool::getArrayVal($configs, 'access.key.secret', '', true);
        if (strlen($keySecret) > 0) {
            throw new OSSException('帐号密码不能为空', ErrorCode::OSS_PARAM_ERROR);
        }

        $addressInner = (string)Tool::getArrayVal($configs, 'server.address.inner', '', true);
        if(preg_match('/^(http|https)\:\/\/\S+$/', $addressInner) == 0){
            throw new OSSException('内网URL不合法', ErrorCode::OSS_PARAM_ERROR);
        }

        $addressOuter = (string)Tool::getArrayVal($configs, 'server.address.outer', '', true);
        if(preg_match('/^(http|https)\:\/\/\S+$/', $addressOuter) == 0){
            throw new OSSException('外网URL不合法', ErrorCode::OSS_PARAM_ERROR);
        }

        $bucketName = (string)Tool::getArrayVal($configs, 'bucket.name', '', true);
        $this->setBucketName($bucketName);

        $bucketDomain = (string)Tool::getArrayVal($configs, 'bucket.domain', '', true);
        $this->setBucketDomain($bucketDomain);

        try {
            $this->innerClient = new OssClient($keyId, $keySecret, $addressInner);
            $this->outerClient = new OssClient($keyId, $keySecret, $addressOuter);
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            $this->bucketName = '';
            $this->bucketDomain = '';
            $this->innerClient = null;
            $this->outerClient = null;

            throw new OSSException('OSS连接出错', ErrorCode::OSS_CONNECT_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getBucketName() : string {
        return $this->bucketName;
    }

    /**
     * @param string $bucketName
     * @throws OSSException
     */
    public function setBucketName(string $bucketName) {
        if (preg_match('/^[0-9a-zA-Z]{2,50}$/', $bucketName) > 0) {
            $this->bucketName = $bucketName;
        } else {
            throw new OSSException('bucket名称不合法', ErrorCode::OSS_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getBucketDomain() : string {
        return $this->bucketDomain;
    }

    /**
     * @param string $bucketDomain
     * @throws OSSException
     */
    public function setBucketDomain(string $bucketDomain) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $bucketDomain) > 0){
            $this->bucketDomain = $bucketDomain;
        } else {
            throw new OSSException('bucket域名不合法', ErrorCode::OSS_PARAM_ERROR);
        }
    }

    /**
     * 上传文件到阿里云
     * @param string $fileName oss上保存的文件名称
     * @param string $file 上传文件路径(包括名称)
     * @return array
     * @throws OSSException
     */
    public function upload($fileName, $file) {
        try {
            $fileInfo = file_get_contents($file);
            $addRes = $this->innerClient->putObject($this->bucketName, $fileName, $fileInfo);

            return $addRes;
        } catch (\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            throw new OSSException(ErrorCode::getMsg(ErrorCode::OSS_UPLOAD_FILE_ERROR), ErrorCode::OSS_UPLOAD_FILE_ERROR);
        }
    }

    /**
     * 删除上传的文件
     * @param string $fileName 上传文件的文件名称，包括路径和后缀
     * @return mixed
     * @throws OSSException
     */
    public function delFile($fileName) {
        try {
            $delRes = $this->outerClient->deleteObject($this->bucketName, $fileName);

            return $delRes;
        } catch(\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            throw new OSSException(ErrorCode::getMsg(ErrorCode::OSS_DELETE_FILE_ERROR), ErrorCode::OSS_DELETE_FILE_ERROR);
        }
    }

    /**
     * 获取bucket中所有文件的文件名称
     * @return array
     * @throws OSSException
     */
    public function getAllFileNames() {
        try {
            $objList = $this->outerClient->listObjects($this->bucketName)->getObjectList();
            $objKeys = [];
            foreach($objList as $eObj) {
                $objKeys[] = $eObj->getKey();
            }

            return $objKeys;
        } catch(\Exception $e) {
            Log::error($e->getMessage(), $e->getCode(), $e->getTraceAsString());

            throw new OSSException(ErrorCode::getMsg(ErrorCode::OSS_GET_BUCKET_FILE_ERROR), ErrorCode::OSS_GET_BUCKET_FILE_ERROR);
        }
    }
}