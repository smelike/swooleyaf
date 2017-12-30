<?php
/**
 * Created by PhpStorm.
 * User: 姜伟
 * Date: 2017/6/20 0020
 * Time: 14:05
 */
namespace Map\Tencent;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;

abstract class BaseConfig {
    const GET_TYPE_SERVER = 'server'; //获取类型-服务端
    const GET_TYPE_MOBILE = 'mobile'; //获取类型-移动端
    const GET_TYPE_BROWSE = 'browse'; //获取类型-网页端

    public function __construct() {
        $this->output = 'json';
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
     * 页面URL
     * @var string
     */
    private $webUrl = '';
    /**
     * 手机应用标识符
     * @var string
     */
    private $appIdentifier = '';
    /**
     * 返回格式,默认JSON
     * @var string
     */
    private $output = '';

    /**
     * @return string
     */
    public function getServerIp() : string {
        return $this->serverIp;
    }

    /**
     * @param string $serverIp
     * @throws \Exception\Map\TencentMapException
     */
    public function setServerIp(string $serverIp) {
        if(preg_match('/^(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){3}$/', $serverIp) > 0){
            $this->serverIp = $serverIp;
        } else {
            throw new TencentMapException('服务端IP不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getWebUrl() : string {
        return $this->webUrl;
    }

    /**
     * @param string $webUrl
     * @throws \Exception\Map\TencentMapException
     */
    public function setWebUrl(string $webUrl) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $webUrl) > 0){
            $this->webUrl = $webUrl;
        } else {
            throw new TencentMapException('页面URL不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAppIdentifier() : string {
        return $this->appIdentifier;
    }

    /**
     * @param string $appIdentifier
     * @throws \Exception\Map\TencentMapException
     */
    public function setAppIdentifier(string $appIdentifier) {
        $identifier = trim($appIdentifier);
        if(strlen($identifier) > 0){
            $this->appIdentifier = $identifier;
        } else {
            throw new TencentMapException('应用标识符不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getOutput() : string {
        return $this->output;
    }

    /**
     * 通过类型获取内容
     * @param string $getType
     * @param array $configs
     * @return string
     * @throws \Exception\Map\TencentMapException
     */
    public function getContentByType(string $getType,array &$configs) : string {
        if($getType == self::GET_TYPE_BROWSE){
            if(strlen($this->webUrl) == 0){
                throw new TencentMapException('页面URL不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
            }

            $content = $this->webUrl;
            $configs['referer'] = $this->webUrl;
        } else if($getType == self::GET_TYPE_MOBILE){
            if(strlen($this->appIdentifier) == 0){
                throw new TencentMapException('应用标识符不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
            }

            $content = $this->appIdentifier;
            $configs['referer'] = $this->appIdentifier;
        } else if($getType == self::GET_TYPE_SERVER){
            if(strlen($this->serverIp) == 0){
                throw new TencentMapException('服务端IP不能为空', ErrorCode::MAP_TENCENT_PARAM_ERROR);
            }

            $content = $this->serverIp;
            if(isset($configs['headers']) && is_array($configs['headers'])){
                $configs['headers'][] = 'X-FORWARDED-FOR:' . $content;
                $configs['headers'][] = 'CLIENT-IP:' . $content;
            } else {
                $configs['headers'] = [
                    'X-FORWARDED-FOR:' . $content,
                    'CLIENT-IP:' . $content
                ];
            }
        } else {
            throw new TencentMapException('获取类型不支持', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }

        return $content;
    }
}