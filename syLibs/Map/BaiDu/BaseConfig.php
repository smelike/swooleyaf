<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/6/20 0020
 * Time: 14:05
 */
namespace Map\BaiDu;

use Constant\ErrorCode;
use Exception\Map\BaiduMapException;

abstract class BaseConfig {
    const CHECK_TYPE_SERVER_IP = 'server-ip'; //校验类型-服务端ip
    const CHECK_TYPE_SERVER_SN = 'server-sn'; //校验类型-服务端签名
    const CHECK_TYPE_BROWSE = 'browse'; //校验类型-浏览器

    public function __construct() {
        $this->output = 'json';
        $this->checkType = self::CHECK_TYPE_SERVER_IP;
    }

    private function __clone() {
    }

    public function getConfigs() : array {
        return get_object_vars($this);
    }

    /**
     * 服务端IP
     * @var string
     */
    private $serverIp = '';
    /**
     * 用户签名
     * @var string
     */
    private $sk = '';
    /**
     * 输出格式
     * @var string
     */
    private $output = '';
    /**
     * 校验类型
     * @var string
     */
    private $checkType = '';
    /**
     * 请求引用地址
     * @var string
     */
    private $reqReferer = '';

    /**
     * @return string
     */
    public function getServerIp() : string {
        return $this->serverIp;
    }

    /**
     * @param string $serverIp
     * @throws \Exception\Map\BaiduMapException
     */
    public function setServerIp(string $serverIp) {
        if(preg_match('/^(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){3}$/', $serverIp) > 0){
            $this->serverIp = $serverIp;
        } else {
            throw new BaiduMapException('服务端IP不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getSk() : string {
        return $this->sk;
    }

    /**
     * @param string $sk
     * @throws \Exception\Map\BaiduMapException
     */
    public function setSk(string $sk) {
        if(preg_match('/^[0-9a-zA-Z]{32}$/', $sk) > 0){
            $this->sk = $sk;
        } else {
            throw new BaiduMapException('用户签名不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getOutput() : string {
        return $this->output;
    }

    /**
     * @return string
     */
    public function getCheckType() : string {
        return $this->checkType;
    }

    /**
     * @param string $checkType
     * @throws \Exception\Map\BaiduMapException
     */
    public function setCheckType(string $checkType) {
        if(in_array($checkType, [self::CHECK_TYPE_SERVER_IP, self::CHECK_TYPE_SERVER_SN, self::CHECK_TYPE_BROWSE], true)){
            $this->checkType = $checkType;
        } else {
            throw new BaiduMapException('校验类型不支持', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getReqReferer() : string {
        return $this->reqReferer;
    }

    /**
     * @param string $reqReferer
     * @throws \Exception\Map\BaiduMapException
     */
    public function setReqReferer(string $reqReferer) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $reqReferer) > 0){
            $this->reqReferer = $reqReferer;
        } else {
            throw new BaiduMapException('请求引用地址不合法', ErrorCode::MAP_BAIDU_PARAM_ERROR);
        }
    }
}