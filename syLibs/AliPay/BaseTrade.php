<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 17-4-11
 * Time: 上午12:25
 */
namespace AliPay;

use DesignPatterns\Singletons\AliConfigSingleton;
use Tool\Tool;

abstract class BaseTrade {
    public function __construct() {
        $this->app_id = AliConfigSingleton::getInstance()->getPayBaseConfig()->getAppId();
        $this->format = 'json';
        $this->charset = 'utf-8';
        $this->sign_type = 'RSA2';
        $this->timestamp = date('Y-m-d H:i:s');
        $this->version = '1.0';
    }

    /**
     * 支付宝分配给开发者的应用ID
     * @var string
     */
    private $app_id = '';

    /**
     * 接口名称
     * @var string
     */
    private $method = '';

    /**
     * 数据格式
     * @var string
     */
    private $format = '';

    /**
     * 请求使用的编码格式
     * @var string
     */
    private $charset = '';

    /**
     * 商户生成签名字符串所使用的签名算法类型，目前支持RSA2和RSA，推荐使用RSA2
     * @var string
     */
    private $sign_type = '';

    /**
     * 商户请求参数的签名串
     * @var string
     */
    private $sign = '';

    /**
     * 发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
     * @var string
     */
    private $timestamp = '';

    /**
     * 调用的接口版本，固定为：1.0
     * @var string
     */
    private $version = '';

    /**
     * 业务请求参数的集合
     * @var array
     */
    private $biz_content = [];

    /**
     * @param string $method
     */
    protected function setMethod(string $method) {
        $this->method = $method;
    }

    /**
     * @param string $key 键名
     * @param mixed $value 键值
     */
    protected function setBizContent(string $key, $value) {
        $this->biz_content[$key] = $value;
    }

    /**
     * @return array
     */
    protected function getBizContent() : array {
        return $this->biz_content;
    }

    protected function getContentArr() : array {
        return [
            'app_id' => $this->app_id,
            'method' => $this->method,
            'format' => $this->format,
            'charset' => $this->charset,
            'sign_type' => $this->sign_type,
            'timestamp' => $this->timestamp,
            'version' => $this->version,
            'biz_content' => Tool::jsonEncode($this->biz_content, JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * 获取订单详情信息
     * @return array
     */
    abstract public function getDetail() : array;
}