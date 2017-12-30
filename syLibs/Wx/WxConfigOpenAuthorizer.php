<?php
/**
 * 微信开放平台授权者配置类
 * User: 姜伟
 * Date: 2017/6/13 0013
 * Time: 19:02
 */
namespace Wx;

use Constant\ErrorCode;
use Exception\Wx\WxOpenException;
use Tool\Tool;

class WxConfigOpenAuthorizer {
    public function __construct() {
    }

    private function __clone() {
    }

    /**
     * 授权者微信号
     * @var string
     */
    private $appId = '';

    /**
     * 授权者授权码
     * @var string
     */
    private $authCode = '';

    /**
     * 授权者刷新令牌
     * @var string
     */
    private $refreshToken = '';

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
            throw new WxOpenException('授权者微信号不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAuthCode(): string {
        return $this->authCode;
    }

    /**
     * @param string $authCode
     * @throws WxOpenException
     */
    public function setAuthCode(string $authCode) {
        if(preg_match('/^queryauthcode\@{3}[0-9a-zA-Z\-\_]+$/', $authCode) > 0){
            $this->authCode = $authCode;
        } else {
            throw new WxOpenException('授权者授权码不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getRefreshToken() : string {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @throws WxOpenException
     */
    public function setRefreshToken(string $refreshToken) {
        if(preg_match('/^refreshtoken\@{3}[0-9a-zA-Z\-\_]+$/', $refreshToken) > 0){
            $this->refreshToken = $refreshToken;
        } else {
            throw new WxOpenException('授权者刷新令牌不合法', ErrorCode::WXOPEN_PARAM_ERROR);
        }
    }

    public function __toString() {
        return Tool::jsonEncode([
            'appid' => $this->appId,
            'authcode' => $this->authCode,
            'refreshtoken' => $this->refreshToken,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取配置数组
     * @return array
     * @throws WxOpenException
     */
    public function getConfigs() : array {
        $appId = trim($this->appId);
        if(strlen($appId) == 0){
            throw new WxOpenException('授权者微信号不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $authCode = trim($this->authCode);
        if(strlen($authCode) == 0){
            throw new WxOpenException('授权者授权码不能为空', ErrorCode::WXOPEN_PARAM_ERROR);
        }

        $refreshToken = trim($this->refreshToken);
        if(strlen($refreshToken) == 0){
            $authData = WxOpenUtil::getAuthorizerAuth($authCode);
            if ($authData['code'] > 0) {
                throw new WxOpenException($authData['message'], ErrorCode::WXOPEN_PARAM_ERROR);
            }

            $refreshToken = $authData['data']['authorization_info']['authorizer_refresh_token'];
        }

        return [
            'appid' => $appId,
            'authcode' => $authCode,
            'refreshtoken' => $refreshToken,
        ];
    }
}