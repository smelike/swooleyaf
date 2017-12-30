<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-16
 * Time: 2:16
 */
namespace AliDaYu;

use DesignPatterns\Singletons\AliConfigSingleton;

abstract class DaYuSmsBase {
    /**
     * API接口名称
     * @var string
     */
    private $method = '';
    /**
     * 应用标识
     * @var string
     */
    private $appKey = '';
    /**
     * 签名的摘要算法
     * @var string
     */
    private $signMethod = '';
    /**
     * 响应格式
     * @var string
     */
    private $format = '';
    /**
     * API协议版本
     * @var string
     */
    private $version = '';
    /**
     * 时间戳
     * @var string
     */
    private $timestamp = '';

    public function __construct(string $method){
        $this->method = $method;
        $this->appKey = AliConfigSingleton::getInstance()->getDaYuConfig()->getAppKey();
        $this->signMethod = 'md5';
        $this->format = 'json';
        $this->version = '2.0';
        $this->timestamp = date('Y-m-d H:i:s');
    }

    private function __clone(){
    }

    public function getBaseDetail() : array {
        return [
            'v' => $this->version,
            'app_key' => $this->appKey,
            'sign_method' => $this->signMethod,
            'format' => $this->format,
            'method' => $this->method,
            'timestamp' => $this->timestamp,
        ];
    }

    abstract public function getDetail() : array;
}