<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/6/21 0021
 * Time: 14:24
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;

class ReqCheck {
    public function __construct() {
    }

    private function __clone() {
        $this->reqMethod = 'GET';
    }

    /**
     * 操作对象
     * @var \Map\BaiDu\BaseConfig
     */
    private $obj = null;
    /**
     * 请求数据
     * @var array
     */
    private $reqData = [];
    /**
     * 请求配置
     * @var array
     */
    private $reqConfigs = [];
    /**
     * 请求地址
     * @var string
     */
    private $reqUrl = '';
    /**
     * 请求方式
     * @var string
     */
    private $reqMethod = '';

    /**
     * @param \Map\BaiDu\BaseConfig $obj
     */
    public function setObj(BaseConfig $obj) {
        $this->obj = $obj;
    }

    /**
     * @return array
     */
    public function getReqData() : array {
        return $this->reqData;
    }

    /**
     * @param array $reqData
     */
    public function setReqData(array $reqData) {
        $this->reqData = $reqData;
    }

    /**
     * @return array
     */
    public function getReqConfigs() : array {
        return $this->reqConfigs;
    }

    /**
     * @param array $reqConfigs
     */
    public function setReqConfigs(array $reqConfigs) {
        $this->reqConfigs = $reqConfigs;
    }

    /**
     * @param string $reqUrl
     * @throws \Exception\Map\BaiduMapException
     */
    public function setReqUrl(string $reqUrl) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $reqUrl) > 0){
            $this->reqUrl = $reqUrl;
        } else {
            throw new BaiduMapException('请求地址不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @param string $reqMethod
     * @throws \Exception\Map\BaiduMapException
     */
    public function setReqMethod(string $reqMethod) {
        if(in_array($reqMethod, ['GET', 'POST'], true)){
            $this->reqMethod = $reqMethod;
        } else {
            throw new BaiduMapException('请求方式不支持', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * 根据校验类型检查请求数据
     * @throws \Exception\Map\BaiduMapException
     */
    public function checkReq() {
        if(is_null($this->obj)){
            throw new BaiduMapException('操作对象不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }

        if($this->obj->getCheckType() == BaseConfig::CHECK_TYPE_SERVER_IP){
            if(strlen($this->obj->getServerIp()) == 0){
                throw new BaiduMapException('服务端IP不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            if(isset($this->reqConfigs['headers']) && is_array($this->reqConfigs['headers'])){
                $this->reqConfigs['headers'][] = 'X-FORWARDED-FOR:' . $this->obj->getServerIp();
                $this->reqConfigs['headers'][] = 'CLIENT-IP:' . $this->obj->getServerIp();
            } else {
                $this->reqConfigs['headers'] = [
                    'X-FORWARDED-FOR:' . $this->obj->getServerIp(),
                    'CLIENT-IP:' . $this->obj->getServerIp(),
                ];
            }
        } else if($this->obj->getCheckType() == BaseConfig::CHECK_TYPE_SERVER_SN){
            if(strlen($this->obj->getSk()) == 0){
                throw new BaiduMapException('签名校验码不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }
            if(strlen($this->reqUrl) == 0){
                throw new BaiduMapException('请求地址不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }
            if(empty($this->reqData)){
                throw new BaiduMapException('请求数据不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            if ($this->reqMethod === 'POST'){
                ksort($this->reqData);
            }
            $str = $this->reqUrl . '?' . http_build_query($this->reqData) . $this->obj->getSk();
            $this->reqData['sn'] = md5(urlencode($str));
        } else if($this->obj->getCheckType() == BaseConfig::CHECK_TYPE_BROWSE){
            if(strlen($this->obj->getReqReferer()) == 0){
                throw new BaiduMapException('请求引用地址不能为空', ErrorCode::MAP_BAIDU_PARAM_ERROR);
            }

            $this->reqConfigs['referer'] = $this->obj->getReqReferer();
            $this->reqConfigs['user_agent'] = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11';
        }
    }
}