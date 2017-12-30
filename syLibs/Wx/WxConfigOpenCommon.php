<?php
/**
 * 微信开放平台公共配置类
 * User: 姜伟
 * Date: 2017/6/13 0013
 * Time: 20:41
 */
namespace Wx;

use Constant\ErrorCode;
use Exception\Wx\WxOpenException;
use Tool\Tool;

class WxConfigOpenCommon {
    public function __construct(){
    }

    private function __clone() {
    }

    /**
     * 开放平台access token超时时间,单位为秒
     * @var int
     */
    private $expireComponentAccessToken = 0;

    /**
     * 授权者access token超时时间,单位为秒
     * @var int
     */
    private $expireAuthorizerAccessToken = 0;

    /**
     * 授权者js ticket超时时间,单位为秒
     * @var int
     */
    private $expireAuthorizerJsTicket = 0;

    /**
     * 开放平台微信号
     * @var string
     */
    private $appId = '';

    /**
     * 开放平台随机密钥
     * @var string
     */
    private $secret = '';

    /**
     * 开放平台消息校验token
     * @var string
     */
    private $token = '';

    /**
     * 开放平台旧消息加解密key
     * @var string
     */
    private $aesKeyBefore = '';

    /**
     * 开放平台新消息加解密key
     * @var string
     */
    private $aesKeyNow = '';

    /**
     * 开放平台授权页面域名
     * @var string
     */
    private $authUrlDomain = '';

    /**
     * 开放平台授权页面回跳地址
     * @var string
     */
    private $authUrlCallback = '';

    /**
     * @return int
     */
    public function getExpireComponentAccessToken(): int {
        return $this->expireComponentAccessToken;
    }

    /**
     * @param int $expireComponentAccessToken
     * @throws WxOpenException
     */
    public function setExpireComponentAccessToken(int $expireComponentAccessToken) {
        if(($expireComponentAccessToken > 0) && ($expireComponentAccessToken <= 7200)){
            $this->expireComponentAccessToken = $expireComponentAccessToken;
        } else {
            throw new WxOpenException('开放平台access token超时时间不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getExpireAuthorizerAccessToken(): int {
        return $this->expireAuthorizerAccessToken;
    }

    /**
     * @param int $expireAuthorizerAccessToken
     * @throws WxOpenException
     */
    public function setExpireAuthorizerAccessToken(int $expireAuthorizerAccessToken) {
        if(($expireAuthorizerAccessToken > 0) && ($expireAuthorizerAccessToken <= 7200)){
            $this->expireAuthorizerAccessToken = $expireAuthorizerAccessToken;
        } else {
            throw new WxOpenException('授权者access token超时时间不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return int
     */
    public function getExpireAuthorizerJsTicket(): int {
        return $this->expireAuthorizerJsTicket;
    }

    /**
     * @param int $expireAuthorizerJsTicket
     * @throws WxOpenException
     */
    public function setExpireAuthorizerJsTicket(int $expireAuthorizerJsTicket) {
        if(($expireAuthorizerJsTicket > 0) && ($expireAuthorizerJsTicket <= 7200)){
            $this->expireAuthorizerJsTicket = $expireAuthorizerJsTicket;
        } else {
            throw new WxOpenException('授权者js ticket超时时间不合法', ErrorCode::WXOPEN_PARAM_ERROR);
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
     * @throws WxOpenException
     */
    public function setAppId(string $appId) {
        if(preg_match('/^[0-9a-z]{18}$/', $appId) > 0){
            $this->appId = $appId;
        } else {
            throw new WxOpenException('appid不合法', ErrorCode::WXOPEN_PARAM_ERROR);
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
     * @throws WxOpenException
     */
    public function setSecret(string $secret) {
        if(preg_match('/^[0-9a-z]{32}$/', $secret) > 0){
            $this->secret = $secret;
        } else {
            throw new WxOpenException('secret不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @param string $token
     * @throws WxOpenException
     */
    public function setToken(string $token) {
        if(preg_match('/^[0-9a-zA-Z]{1,32}$/', $token) > 0){
            $this->token = $token;
        } else {
            throw new WxOpenException('消息校验Token不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAesKeyBefore(): string {
        return $this->aesKeyBefore;
    }

    /**
     * @param string $aesKeyBefore
     * @throws WxOpenException
     */
    public function setAesKeyBefore(string $aesKeyBefore) {
        if(preg_match('/^[0-9a-zA-Z]{43}$/', $aesKeyBefore) > 0){
            $this->aesKeyBefore = $aesKeyBefore;
        } else {
            throw new WxOpenException('旧消息加解密Key不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAesKeyNow(): string {
        return $this->aesKeyNow;
    }

    /**
     * @param string $aesKeyNow
     * @throws WxOpenException
     */
    public function setAesKeyNow(string $aesKeyNow) {
        if(preg_match('/^[0-9a-zA-Z]{43}$/', $aesKeyNow) > 0){
            $this->aesKeyNow = $aesKeyNow;
        } else {
            throw new WxOpenException('新消息加解密Key不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAuthUrlDomain(): string {
        return $this->authUrlDomain;
    }

    /**
     * @param string $authUrlDomain
     * @throws WxOpenException
     */
    public function setAuthUrlDomain(string $authUrlDomain) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $authUrlDomain) > 0){
            $this->authUrlDomain = $authUrlDomain;
        } else {
            throw new WxOpenException('授权页面URL不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAuthUrlCallback(): string {
        return $this->authUrlCallback;
    }

    /**
     * @param string $authUrlCallback
     * @throws WxOpenException
     */
    public function setAuthUrlCallback(string $authUrlCallback) {
        if(preg_match('/^(http|https)\:\/\/\S+$/', $authUrlCallback) > 0){
            $this->authUrlCallback = $authUrlCallback;
        } else {
            throw new WxOpenException('授权页面回跳URL不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    public function __toString(){
        return Tool::jsonEncode([
            'appid' => $this->appId,
            'token' => $this->token,
            'secret' => $this->secret,
            'aeskey.now' => $this->aesKeyNow,
            'aeskey.before' => $this->aesKeyBefore,
            'authurl.domain' => $this->authUrlDomain,
            'authurl.callback' => $this->authUrlCallback,
            'expire.component.accesstoken' => $this->expireComponentAccessToken,
            'expire.authorizer.jsticket' => $this->expireAuthorizerJsTicket,
            'expire.authorizer.accesstoken' => $this->expireAuthorizerAccessToken,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function getConfigs() : array {
        return [
            'appid' => $this->appId,
            'token' => $this->token,
            'secret' => $this->secret,
            'aeskey.now' => $this->aesKeyNow,
            'aeskey.before' => $this->aesKeyBefore,
            'authurl.domain' => $this->authUrlDomain,
            'authurl.callback' => $this->authUrlCallback,
            'expire.component.accesstoken' => $this->expireComponentAccessToken,
            'expire.authorizer.jsticket' => $this->expireAuthorizerJsTicket,
            'expire.authorizer.accesstoken' => $this->expireAuthorizerAccessToken,
        ];
    }
}