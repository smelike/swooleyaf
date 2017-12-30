<?php
/**
 * IP定位
 * User: jw
 * Date: 17-6-21
 * Time: 上午12:08
 */
namespace Map\Tencent;

use Constant\ErrorCode;
use Exception\Map\TencentMapException;
use Tool\Tool;

class IpLocation extends BaseConfig {
    public function __construct() {
        parent::__construct();
    }

    private function __clone() {
    }

    public function __toString() {
        $vars = array_merge(get_object_vars($this), parent::getConfigs());

        return Tool::jsonEncode($vars, JSON_UNESCAPED_UNICODE);
    }

    /**
     * IP
     * @var string
     */
    private $ip = '';

    /**
     * @return string
     */
    public function getIp() : string {
        return $this->ip;
    }

    /**
     * @param string $ip
     * @throws \Exception\Map\TencentMapException
     */
    public function setIp(string $ip) {
        if (preg_match('/^(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])(\.(\d|[1-9]\d|1\d{2}|2[0-4]\d|25[0-5])){3}$/', $ip) > 0) {
            $this->ip = $ip;
        } else {
            throw new TencentMapException('ip不合法', ErrorCode::MAP_TENCENT_PARAM_ERROR);
        }
    }
}