<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-06-18
 * Time: 15:32
 */
namespace AliConfigs;

use Constant\ErrorCode;
use Exception\Ali\AliDaYuException;
use Tool\Tool;

class DaYu {
    public function __construct() {
    }

    private function __clone() {
    }

    /**
     * APP KEY
     * @var string
     */
    private $appKey = '';
    /**
     * APP 密钥
     * @var string
     */
    private $appSecret = '';

    /**
     * @return string
     */
    public function getAppKey() : string {
        return $this->appKey;
    }

    /**
     * @param string $appKey
     * @throws AliDaYuException
     */
    public function setAppKey(string $appKey) {
        if (preg_match('/^\d{8}$/', $appKey) > 0) {
            $this->appKey = $appKey;
        } else {
            throw new AliDaYuException('app key不合法', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
    }

    /**
     * @return string
     */
    public function getAppSecret() : string {
        return $this->appSecret;
    }

    /**
     * @param string $appSecret
     * @throws AliDaYuException
     */
    public function setAppSecret(string $appSecret) {
        if (preg_match('/^[0-9a-z]{32}$/', $appSecret) > 0) {
            $this->appSecret = $appSecret;
        } else {
            throw new AliDaYuException('app secret不合法', ErrorCode::ALIDAYU_PARAM_ERROR);
        }
    }

    public function __toString() {
        return Tool::jsonEncode([
            'app.key' => $this->appKey,
            'app.secret' => $this->appSecret,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取配置数组
     * @return array
     */
    public function getConfigs() : array {
        return [
            'app.key' => $this->appKey,
            'app.secret' => $this->appSecret,
        ];
    }
}