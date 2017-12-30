<?php
/**
 * 微信商户平台配置类
 * User: 姜伟
 * Date: 2017/6/13 0013
 * Time: 19:01
 */
namespace Wx;

use Constant\ErrorCode;
use Exception\Wx\WxException;
use Tool\Tool;

class WxConfigShop {
    public function __construct() {
    }

    private function __clone() {
    }

    /**
     * js ticket超时时间,单位为秒
     * @var int
     */
    private $expireJsTicket = 0;

    /**
     * access token超时时间,单位为秒
     * @var int
     */
    private $expireAccessToken = 0;

    /**
     * 客户端IP
     * @var string
     */
    private $clientIp = '';

    /**
     * 微信号
     * @var string
     */
    private $appId = '';

    /**
     * 微信随机密钥
     * @var string
     */
    private $secret = '';

    /**
     * 商户号
     * @var string
     */
    private $payMchId = '';

    /**
     * 商户支付密钥
     * @var string
     */
    private $payKey = '';

    /**
     * 支付异步通知URL
     * @var string
     */
    private $payNotifyUrl = '';

    /**
     * 支付授权URL
     * @var string
     */
    private $payAuthUrl = '';

    /**
     * CERT PEM证书路径
     * @var string
     */
    private $sslCert = '';

    /**
     * KEY PEM证书路径
     * @var string
     */
    private $sslKey = '';

    /**
     * 模板列表
     * @var array
     */
    private $templates = [];

    /**
     * @return int
     */
    public function getExpireJsTicket(): int {
        return $this->expireJsTicket;
    }

    /**
     * @param int $expireJsTicket
     * @throws \Exception\WX\WxException
     */
    public function setExpireJsTicket(int $expireJsTicket) {
        if(($expireJsTicket > 0) && ($expireJsTicket <= 7200)){
            $this->expireJsTicket = $expireJsTicket;
        } else {
            throw new WxException('js ticket超时时间不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getExpireAccessToken(): int {
        return $this->expireAccessToken;
    }

    /**
     * @param int $expireAccessToken
     * @throws \Exception\WX\WxException
     */
    public function setExpireAccessToken(int $expireAccessToken) {
        if(($expireAccessToken > 0) && ($expireAccessToken <= 7200)){
            $this->expireAccessToken = $expireAccessToken;
        } else {
            throw new WxException('access token超时时间不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getClientIp(): string {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     * @throws \Exception\WX\WxException
     */
    public function setClientIp(string $clientIp) {
        if(preg_match('/^(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){3}$/', $clientIp) > 0){
            $this->clientIp = $clientIp;
        } else {
            throw new WxException('客户端IP不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAppId(): string {
        return $this->appId;
    }

    /**
     * @param string $appId
     * @throws \Exception\WX\WxException
     */
    public function setAppId(string $appId) {
        if(preg_match('/^[0-9a-z]{18}$/', $appId) > 0){
            $this->appId = $appId;
        } else {
            throw new WxException('app id不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getSecret(): string {
        return $this->secret;
    }

    /**
     * @param string $secret
     * @throws \Exception\WX\WxException
     */
    public function setSecret(string $secret) {
        if(preg_match('/^[0-9a-z]{32}$/', $secret) > 0){
            $this->secret = $secret;
        } else {
            throw new WxException('secret不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPayMchId(): string {
        return $this->payMchId;
    }

    /**
     * @param string $payMchId
     * @throws \Exception\WX\WxException
     */
    public function setPayMchId(string $payMchId) {
        if(preg_match('/^\d{10}$/', $payMchId) > 0){
            $this->payMchId = $payMchId;
        } else {
            throw new WxException('商户号不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPayKey(): string {
        return $this->payKey;
    }

    /**
     * @param string $payKey
     * @throws \Exception\WX\WxException
     */
    public function setPayKey(string $payKey) {
        if(preg_match('/^[0-9a-zA-Z]{32}$/', $payKey) > 0){
            $this->payKey = $payKey;
        } else {
            throw new WxException('支付密钥不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPayNotifyUrl(): string {
        return $this->payNotifyUrl;
    }

    /**
     * @param string $payNotifyUrl
     * @throws \Exception\WX\WxException
     */
    public function setPayNotifyUrl(string $payNotifyUrl) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $payNotifyUrl) > 0){
            $this->payNotifyUrl = $payNotifyUrl;
        } else {
            throw new WxException('支付异步消息通知URL不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getPayAuthUrl(): string {
        return $this->payAuthUrl;
    }

    /**
     * @param string $payAuthUrl
     * @throws \Exception\WX\WxException
     */
    public function setPayAuthUrl(string $payAuthUrl) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $payAuthUrl) > 0){
            $this->payAuthUrl = $payAuthUrl;
        } else {
            throw new WxException('支付授权URL不合法', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getSslCert(): string {
        return $this->sslCert;
    }

    /**
     * @param string $sslCert
     * @throws \Exception\WX\WxException
     */
    public function setSslCert(string $sslCert) {
        if(strlen($sslCert) > 0){
            $this->sslCert = $sslCert;
        } else {
            throw new WxException('cert证书不能为空', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getSslKey(): string {
        return $this->sslKey;
    }

    /**
     * @param string $sslKey
     * @throws \Exception\WX\WxException
     */
    public function setSslKey(string $sslKey) {
        if(strlen($sslKey) > 0){
            $this->sslKey = $sslKey;
        } else {
            throw new WxException('key证书不能为空', ErrorCode::WX_PARAM_ERROR);
        }
    }

    /**
     * @return array
     */
    public function getTemplates() : array {
        return $this->templates;
    }

    /**
     * @param array $templates
     */
    public function setTemplates(array $templates) {
        $this->templates = $templates;
    }

    public function __toString() {
        return Tool::jsonEncode([
            'appid' => $this->appId,
            'secret' => $this->secret,
            'clientip' => $this->clientIp,
            'pay.key' => $this->payKey,
            'pay.mchid' => $this->payMchId,
            'pay.url.auth' => $this->payAuthUrl,
            'pay.url.notify' => $this->payNotifyUrl,
            'ssl.key' => $this->sslKey,
            'ssl.cert' => $this->sslCert,
            'expire.jsticket' => $this->expireJsTicket,
            'expire.accesstoken' => $this->expireAccessToken,
            'templates' => $this->templates,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取配置数组
     * @return array
     */
    public function getConfigs() : array {
        return [
            'appid' => $this->appId,
            'secret' => $this->secret,
            'clientip' => $this->clientIp,
            'pay.key' => $this->payKey,
            'pay.mchid' => $this->payMchId,
            'pay.url.auth' => $this->payAuthUrl,
            'pay.url.notify' => $this->payNotifyUrl,
            'ssl.key' => $this->sslKey,
            'ssl.cert' => $this->sslCert,
            'expire.jsticket' => $this->expireJsTicket,
            'expire.accesstoken' => $this->expireAccessToken,
            'templates' => $this->templates,
        ];
    }
}